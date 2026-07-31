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
        EnvironmentWriter::sanitizeExistingEnv();
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

    private function isPostRequest(): bool
    {
        return strtolower($this->request->getMethod()) === 'post';
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
        if ($this->isPostRequest()) {
            $result = $this->dbInstaller->testConnection($host, $user, $pass, $name, $port);
            if ($result['success']) {
                // Schema is no longer built here via legacy schema.sql/seed.sql
                // (that caused RC0's login failure — see DatabaseInstaller::
                // migrateAndSeedCore() for the full explanation). It's now
                // built via real migrations in admin(), on the request AFTER
                // .env has actually been written with these credentials.
                session()->set([
                    'db_host' => $host,
                    'db_port' => $port,
                    'db_name' => $name,
                    'db_user' => $user,
                    'db_pass' => $pass,
                ]);

                if ($action === 'save' || $action === 'test') {
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

        if ($this->isPostRequest()) {
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
        if ($this->isPostRequest()) {
            // This is the first request since .env was written in
            // application() — safe point to build the real schema.
            $migrateResult = $this->dbInstaller->migrateAndSeedCore();
            if (!$migrateResult['success']) {
                return view('installer/admin', ['message' => $migrateResult['message']]);
            }

            // RC0 UAT: Check duplicate username BEFORE calling AdminSeeder
            $username = trim((string) $this->request->getPost('username'));
            if ($username !== '') {
                try {
                    $db = \Config\Database::connect();
                    if ($db->tableExists('users')) {
                        $existing = $db->table('users')->where('username', $username)->get()->getRow();
                        if ($existing) {
                            return view('installer/admin', [
                                'message' => 'Username "' . esc($username) . '" sudah terdaftar. Silakan pilih username lain.',
                            ]);
                        }
                    }
                } catch (\Throwable $e) {
                    // If DB not ready yet, let AdminSeeder handle it
                }
            }

            $result = $this->adminSeeder->createAdmin($this->request->getPost());
            if ($result['success']) {
                $persistResult = $this->persistAdminUser($result['user']);
                if (!$persistResult['success']) {
                    return view('installer/admin', ['message' => $persistResult['message']]);
                }

                $this->lock->createLock();
                return redirect()->to('install/finish');
            }
            $message = $result['message'];
        }

        return view('installer/admin', ['message' => $message]);
    }

    /**
     * Fix for RC0 login failure (bug #2): AdminSeeder::createAdmin() only
     * validated input and generated a password hash — the resulting user was
     * never actually written to the database, so the account the wizard
     * promised to create simply didn't exist after finishing install.
     */
    private function persistAdminUser(array $user): array
    {
        try {
            $db = \Config\Database::connect();

            $existing = $db->table('users')->where('username', $user['username'])->get()->getRow();
            if ($existing) {
                return ['success' => false, 'message' => 'Username sudah digunakan, silakan pilih username lain.'];
            }

            $userId = 'u-' . bin2hex(random_bytes(8));
            $db->table('users')->insert([
                'id'            => $userId,
                'username'      => $user['username'],
                'email'         => $user['email'],
                'password_hash' => $user['hash'],
                'status'        => 'ACTIVE',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            $superAdminRole = $db->table('roles')->where('role_code', 'SUPER_ADMIN')->get()->getRow();
            if ($superAdminRole) {
                $db->table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $superAdminRole->id,
                ]);
            }

            return ['success' => true, 'message' => 'Admin berhasil dibuat.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal menyimpan akun admin: ' . $e->getMessage()];
        }
    }

    public function finish(): string|ResponseInterface
    {
        return view('installer/finish', ['username' => 'superadmin']);
    }
}
