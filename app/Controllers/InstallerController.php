<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use App\Services\Installer\AdminSeeder;
use App\Services\Installer\DatabaseInstaller;
use App\Services\Installer\EnvironmentWriter;
use App\Services\Installer\InstallerLock;
use App\Services\Installer\RequirementChecker;
use CodeIgniter\HTTP\ResponseInterface;

class InstallerController extends BaseController
{
    private InstallerLock $lock;
    private RequirementChecker $reqChecker;
    private DatabaseInstaller $dbInstaller;
    private EnvironmentWriter $envWriter;
    private AdminSeeder $adminSeeder;

    public function __construct()
    {
        $this->lock = new InstallerLock();
        $this->reqChecker = new RequirementChecker();
        $this->dbInstaller = new DatabaseInstaller();
        $this->envWriter = new EnvironmentWriter();
        $this->adminSeeder = new AdminSeeder();
    }

    private function checkInstalled(): ?ResponseInterface
    {
        if ($this->lock->isInstalled()) {
            return redirect()->to('admin/dashboard');
        }
        return null;
    }

    public function welcome(): string|ResponseInterface
    {
        if ($redirect = $this->checkInstalled()) return $redirect;
        return view('installer/welcome');
    }

    public function requirements(): string|ResponseInterface
    {
        if ($redirect = $this->checkInstalled()) return $redirect;

        return view('installer/requirements', [
            'phpCheck'   => $this->reqChecker->checkPhpVersion(),
            'extChecks'  => $this->reqChecker->checkExtensions(),
            'permChecks' => $this->reqChecker->checkPermissions(),
            'allPass'    => $this->reqChecker->passesAll(),
        ]);
    }

    public function database(): string|ResponseInterface
    {
        if ($redirect = $this->checkInstalled()) return $redirect;

        $host = (string) ($this->request->getPost('db_host') ?? 'localhost');
        $port = (int) ($this->request->getPost('db_port') ?? 3306);
        $name = (string) ($this->request->getPost('db_name') ?? 'masjidcms_db');
        $user = (string) ($this->request->getPost('db_user') ?? 'root');
        $pass = (string) ($this->request->getPost('db_pass') ?? '');
        $action = (string) ($this->request->getPost('action') ?? '');

        $result = null;
        if ($this->request->getMethod() === 'post') {
            $result = $this->dbInstaller->testConnection($host, $user, $pass, $name, $port);
            if ($result['success']) {
                $this->dbInstaller->importSchema($host, $user, $pass, $name, $port);
                
                // Store DB credentials in session for EnvironmentWriter
                session()->set([
                    'db_host' => $host,
                    'db_port' => $port,
                    'db_name' => $name,
                    'db_user' => $user,
                    'db_pass' => $pass,
                ]);

                if ($action === 'save') {
                    return redirect()->to('install/application');
                }
            }
        }

        return view('installer/database', [
            'dbHost'  => $host,
            'dbPort'  => $port,
            'dbName'  => $name,
            'dbUser'  => $user,
            'dbPass'  => $pass,
            'success' => $result['success'] ?? false,
            'message' => $result['message'] ?? '',
        ]);
    }

    public function application(): string|ResponseInterface
    {
        if ($redirect = $this->checkInstalled()) return $redirect;

        if ($this->request->getMethod() === 'post') {
            $config = [
                'app_name' => $this->request->getPost('app_name'),
                'app_url'  => $this->request->getPost('app_url'),
                'db_host'  => session()->get('db_host') ?? 'localhost',
                'db_name'  => session()->get('db_name') ?? 'masjidcms_db',
                'db_user'  => session()->get('db_user') ?? 'root',
                'db_pass'  => session()->get('db_pass') ?? '',
                'db_port'  => session()->get('db_port') ?? '3306',
            ];
            $this->envWriter->writeEnvironment($config);
            return redirect()->to('install/admin');
        }

        return view('installer/application');
    }

    public function admin(): string|ResponseInterface
    {
        if ($redirect = $this->checkInstalled()) return $redirect;

        $message = '';
        if ($this->request->getMethod() === 'post') {
            $result = $this->adminSeeder->createAdmin($this->request->getPost());
            if ($result['success']) {
                $this->lock->createLock();
                return redirect()->to('install/finish');
            }
            $message = $result['message'];
        }

        return view('installer/admin', ['message' => $message]);
    }

    public function finish(): string|ResponseInterface
    {
        return view('installer/finish', ['username' => 'superadmin']);
    }
}
