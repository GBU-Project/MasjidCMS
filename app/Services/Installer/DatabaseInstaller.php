<?php

namespace App\Services\Installer;

class DatabaseInstaller
{
    public function testConnection(string $host, string $user, string $password, string $database, int $port = 3306): array
    {
        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $pdo->exec("USE `{$database}`;");

            return ['success' => true, 'message' => "Koneksi & database '{$database}' berhasil diverifikasi."];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal koneksi database: ' . $e->getMessage()];
        }
    }

    public function importSchema(string $host, string $user, string $password, string $database, int $port = 3306): array
    {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

            $schemaFile = ROOTPATH . 'database/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $pdo->exec($sql);
            }

            $seedFile = ROOTPATH . 'database/seed.sql';
            if (file_exists($seedFile)) {
                $sqlSeed = file_get_contents($seedFile);
                $pdo->exec($sqlSeed);
            }

            return ['success' => true, 'message' => 'Schema & Seed SQL berhasil diimport ke database.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal import SQL: ' . $e->getMessage()];
        }
    }
}
