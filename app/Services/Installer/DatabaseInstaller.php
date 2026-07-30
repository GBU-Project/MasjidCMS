<?php

namespace App\Services\Installer;

class DatabaseInstaller
{
    private function getPdoOptions(): array
    {
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        if (defined('\PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
            $options[\PDO::MYSQL_ATTR_MULTI_STATEMENTS] = true;
        }
        return $options;
    }

    public function testConnection(string $host, string $user, string $password, string $database, int $port = 3306): array
    {
        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $user, $password, $this->getPdoOptions());
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $pdo->exec("USE `{$database}`;");

            return ['success' => true, 'message' => "Koneksi & database '{$database}' berhasil diverifikasi."];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal koneksi database: ' . $e->getMessage()];
        }
    }

    /**
     * Root-cause fix for RC0 login failure: importSchema() below runs the
     * legacy database/schema.sql + database/seed.sql, which predate the
     * `CreateRbacTables` migration (no role_code/permission_code/module_name
     * columns) and are missing dozens of newer tables entirely (agenda,
     * homepage_manager_settings, mosque business modules, etc.).
     * PermissionRepository::findByUserId() joins on `role_permissions
     * .permission_code`, which does not exist under the old schema — so
     * every login fails with a DB error on any install that went through
     * this wizard.
     *
     * This method builds the schema the CORRECT way: via CodeIgniter's own
     * migrations (the real source of truth already used everywhere else in
     * this app), then seeds the RBAC catalog (roles + permissions) via the
     * existing, already-correct RbacSeeder. It must be called on a request
     * AFTER the .env file has been written (see EnvironmentWriter), because
     * PHP only reads .env once at framework bootstrap — calling this in the
     * same request that writes .env would still use the old/blank DB config.
     */
    public function migrateAndSeedCore(): array
    {
        try {
            $runner = \Config\Services::migrations();
            $runner->setNamespace('App')->latest();

            $seeder = \Config\Database::seeder();
            $seeder->call('RbacSeeder');

            return ['success' => true, 'message' => 'Skema database (migrations) & katalog RBAC berhasil disiapkan.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal menjalankan migrations/seeder: ' . $e->getMessage()];
        }
    }

    /**
     * @deprecated Kept for reference only — no longer called by
     * InstallerController. See migrateAndSeedCore() above for why.
     */
    public function importSchema(string $host, string $user, string $password, string $database, int $port = 3306): array
    {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $user, $password, $this->getPdoOptions());

            $schemaFile = ROOTPATH . 'database/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $this->executeSqlQueries($pdo, $sql);
            }

            $seedFile = ROOTPATH . 'database/seed.sql';
            if (file_exists($seedFile)) {
                $sqlSeed = file_get_contents($seedFile);
                $this->executeSqlQueries($pdo, $sqlSeed);
            }

            return ['success' => true, 'message' => 'Schema DDL & Seed SQL berhasil diimport ke database.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal import SQL: ' . $e->getMessage()];
        }
    }

    private function executeSqlQueries(\PDO $pdo, string $sqlContent): void
    {
        try {
            $pdo->exec($sqlContent);
        } catch (\Throwable $e) {
            // Statement fallback loop if multi-query exec is not permitted by PDO driver
            $queries = array_filter(array_map('trim', explode(';', $sqlContent)));
            foreach ($queries as $query) {
                if (!empty($query)) {
                    $pdo->exec($query);
                }
            }
        }
    }
}
