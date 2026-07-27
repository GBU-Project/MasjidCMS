<?php

namespace App\Services\Installer;

class EnvironmentWriter
{
    public function generateAppKey(): string
    {
        return 'hex2bin:' . bin2hex(random_bytes(32));
    }

    public function writeEnvironment(array $config, ?string $targetFile = null): bool
    {
        $file = $targetFile ?? (ROOTPATH . '.env');
        $exampleFile = ROOTPATH . '.env.example';

        $template = file_exists($exampleFile)
            ? file_get_contents($exampleFile)
            : "CI_ENVIRONMENT = production\napp.baseURL = 'http://localhost:8080/'\n";

        // Strip unquoted spaces / invalid session.savePath
        $template = str_replace("session.savePath = WRITEPATH 'session'", "# session.savePath = ''", $template);

        $appKey = $this->generateAppKey();

        $replacements = [
            'CI_ENVIRONMENT = production' => "CI_ENVIRONMENT = " . ($config['app_env'] ?? 'production'),
            "app.baseURL = 'https://masjid.domain.org/'" => "app.baseURL = '" . ($config['app_url'] ?? 'http://localhost:8080/') . "'",
            "database.default.hostname = localhost" => "database.default.hostname = " . ($config['db_host'] ?? 'localhost'),
            "database.default.database = masjidcms_db" => "database.default.database = " . ($config['db_name'] ?? 'masjidcms_db'),
            "database.default.username = masjid_user" => "database.default.username = " . ($config['db_user'] ?? 'root'),
            "database.default.password = secret_db_password" => "database.default.password = '" . ($config['db_pass'] ?? '') . "'",
            "database.default.port = 3306" => "database.default.port = " . ($config['db_port'] ?? '3306'),
            "encryption.key = 'hex2bin:0000000000000000000000000000000000000000000000000000000000000000'" => "encryption.key = '{$appKey}'",
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $template);

        return file_put_contents($file, $content) !== false;
    }

    public static function sanitizeExistingEnv(): void
    {
        $envFile = ROOTPATH . '.env';
        if (file_exists($envFile)) {
            $content = file_get_contents($envFile);
            if (str_contains($content, "WRITEPATH 'session'")) {
                $content = str_replace("session.savePath = WRITEPATH 'session'", "# session.savePath = ''", $content);
                file_put_contents($envFile, $content);
            }
        }
    }
}
