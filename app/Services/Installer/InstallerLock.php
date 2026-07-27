<?php

namespace App\Services\Installer;

class InstallerLock
{
    private string $lockFile;

    public function __construct(?string $lockFile = null)
    {
        $this->lockFile = $lockFile ?? (WRITEPATH . 'installed.lock');
    }

    public function isInstalled(): bool
    {
        return file_exists($this->lockFile);
    }

    public function createLock(): bool
    {
        $dir = dirname($this->lockFile);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents($this->lockFile, 'INSTALLED_ON_' . date('Y-m-d_H:i:s')) !== false;
    }
}
