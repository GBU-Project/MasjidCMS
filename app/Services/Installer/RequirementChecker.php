<?php

namespace App\Services\Installer;

class RequirementChecker
{
    public function checkPhpVersion(): array
    {
        $version = PHP_VERSION;
        $pass = version_compare($version, '8.2.0', '>=');
        return [
            'name'     => 'PHP Version >= 8.2',
            'current'  => $version,
            'required' => '8.2.0',
            'pass'     => $pass,
        ];
    }

    public function checkExtensions(): array
    {
        $requiredExts = ['pdo_mysql', 'mysqli', 'mbstring', 'openssl', 'json', 'curl', 'fileinfo'];
        $results = [];
        foreach ($requiredExts as $ext) {
            $pass = extension_loaded($ext);
            $results[] = [
                'name'    => "Extension: {$ext}",
                'current' => $pass ? 'Installed' : 'Missing',
                'pass'    => $pass,
            ];
        }
        return $results;
    }

    public function checkPermissions(): array
    {
        $directories = [
            WRITEPATH,
            WRITEPATH . 'cache',
            WRITEPATH . 'logs',
            WRITEPATH . 'session',
        ];
        $results = [];
        foreach ($directories as $dir) {
            if (! is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $isWritable = is_writable($dir);
            $results[] = [
                'name'    => 'Writable: ' . basename(rtrim($dir, '/\\')),
                'current' => $isWritable ? 'Writable (OK)' : 'Not Writable',
                'pass'    => $isWritable,
            ];
        }
        return $results;
    }

    public function passesAll(): bool
    {
        if (! $this->checkPhpVersion()['pass']) {
            return false;
        }
        foreach ($this->checkExtensions() as $ext) {
            if (! $ext['pass']) {
                return false;
            }
        }
        foreach ($this->checkPermissions() as $perm) {
            if (! $perm['pass']) {
                return false;
            }
        }
        return true;
    }
}
