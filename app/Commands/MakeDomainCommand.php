<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Class MakeDomainCommand
 *
 * Command CLI CodeIgniter 4 pen-generate Business Domain baru di MasjidCMS.
 * Menghasilkan struktur 17+ file & folder sesuai Golden Domain Template (docs/DOMAIN_TEMPLATE.md).
 */
class MakeDomainCommand extends BaseCommand
{
    protected $group       = 'Generators';
    protected $name        = 'make:domain';
    protected $description = 'Generates a new Business Domain conforming to Golden Domain Template (TASK-018).';
    protected $usage       = 'make:domain <DomainName> [options]';

    protected $arguments = [
        'DomainName' => 'The name of the business domain in PascalCase (e.g. Jamaah, Kajian, Donasi, Keuangan)',
    ];

    protected $options = [
        '--force'     => 'Force overwrite existing domain files if folder exists',
        '--no-seeder' => 'Skip generating database seeder file',
        '--views'     => 'Generate view partials folder',
    ];

    public function run(array $params)
    {
        $domainName = $params[0] ?? CLI::getSegment(2);

        if (empty($domainName)) {
            CLI::error('Error: You must specify a domain name. Example: php spark make:domain Jamaah');
            return EXIT_USER_INPUT;
        }

        // 1. Sanitize & Normalize Input Name
        $domain = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $domainName));
        $entity = $domain;
        $table  = strtolower($domain) . 's';
        $routeSlug = strtolower($domain);
        $namespace = 'App\Domains\\' . $domain;
        $variableSingular = '$' . lcfirst($domain);
        $variablePlural   = '$' . lcfirst($domain) . 's';
        $timestamp = date('Y-m-d-His_');
        $year = date('Y');

        $force = CLI::getOption('force') !== null;
        $noSeeder = CLI::getOption('no-seeder') !== null;
        $generateViews = CLI::getOption('views') !== null;

        CLI::write(sprintf('*** Generating Business Domain [%s] ***', $domain), 'green');

        // 2. Conflict Strategy Check
        $domainPath = APPPATH . 'Domains/' . $domain . '/';
        if (is_dir($domainPath) && !$force) {
            CLI::error(sprintf('ABORT: Domain folder [%s] already exists! Use --force to overwrite.', $domainPath));
            return EXIT_USER_INPUT;
        }

        // 3. Prepare Placeholder Values Map
        $placeholders = [
            '{{Domain}}'           => $domain,
            '{{Entity}}'           => $entity,
            '{{Table}}'            => $table,
            '{{RouteSlug}}'        => $routeSlug,
            '{{Namespace}}'        => $namespace,
            '{{VariableSingular}}' => $variableSingular,
            '{{VariablePlural}}'   => $variablePlural,
            '{{Timestamp}}'        => $timestamp,
            '{{Year}}'             => $year,
        ];

        // 4. Create Domain Directories
        $directories = [
            $domainPath . 'Config',
            $domainPath . 'Controllers',
            $domainPath . 'DTO',
            $domainPath . 'Entities',
            $domainPath . 'Models',
            $domainPath . 'Policies',
            $domainPath . 'Providers',
            $domainPath . 'Repositories',
            $domainPath . 'Routes',
            $domainPath . 'Services',
            $domainPath . 'Validation',
            $domainPath . 'Database/Migrations',
            $domainPath . 'Database/Seeds',
            $domainPath . 'Views',
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $stubsDir = __DIR__ . '/Stubs/';

        // 5. Generate Domain Files
        $fileMappings = [
            'Config.stub'     => $domainPath . 'Config/' . $domain . 'Config.php',
            'Controller.stub' => $domainPath . 'Controllers/' . $entity . 'Controller.php',
            'CreateDTO.stub'  => $domainPath . 'DTO/Create' . $entity . 'DTO.php',
            'UpdateDTO.stub'  => $domainPath . 'DTO/Update' . $entity . 'DTO.php',
            'Entity.stub'     => $domainPath . 'Entities/' . $entity . '.php',
            'Model.stub'      => $domainPath . 'Models/' . $entity . 'Model.php',
            'Policy.stub'     => $domainPath . 'Policies/' . $entity . 'Policy.php',
            'Provider.stub'   => $domainPath . 'Providers/' . $domain . 'Provider.php',
            'Repository.stub' => $domainPath . 'Repositories/' . $domain . 'Repository.php',
            'Route.stub'      => $domainPath . 'Routes/' . $routeSlug . '.php',
            'Service.stub'    => $domainPath . 'Services/' . $domain . 'Service.php',
            'README.stub'     => $domainPath . 'README.md',
        ];

        // System Documentation & Test Mappings
        $migrationFile = APPPATH . 'Database/Migrations/' . $timestamp . 'Create' . $entity . 'sTable.php';
        $seederFile    = APPPATH . 'Database/Seeds/' . $entity . 'Seeder.php';
        $adrFile       = ROOTPATH . 'docs/adr/ADR-00' . rand(7, 9) . '-' . $domain . '-Domain.md';
        $uatFile       = ROOTPATH . 'docs/UAT/' . $domain . '-UAT.md';
        $testFile      = ROOTPATH . 'tests/unit/Domains/' . $domain . 'DomainTest.php';

        $fileMappings['Migration.stub'] = $migrationFile;
        if (!$noSeeder) {
            $fileMappings['Seeder.stub'] = $seederFile;
        }
        $fileMappings['ADR.stub']  = $adrFile;
        $fileMappings['UAT.stub']  = $uatFile;
        $fileMappings['Test.stub'] = $testFile;

        // 6. Write Files with Placeholder Resolution
        foreach ($fileMappings as $stubName => $targetPath) {
            $stubPath = $stubsDir . $stubName;
            if (!file_exists($stubPath)) {
                CLI::write(sprintf('  [WARNING] Stub [%s] missing. Skipping.', $stubName), 'yellow');
                continue;
            }

            // Ensure parent directory exists
            $targetDir = dirname($targetPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Check existing file strategy
            if (file_exists($targetPath) && !$force) {
                CLI::write(sprintf('  [SKIP] File [%s] already exists.', basename($targetPath)), 'yellow');
                continue;
            }

            $stubContent = file_get_contents($stubPath);
            $parsedContent = strtr($stubContent, $placeholders);

            file_put_contents($targetPath, $parsedContent);
            CLI::write(sprintf('  [CREATED] %s', str_replace(ROOTPATH, '', $targetPath)), 'green');
        }

        // 7. Idempotent Route Auto-Registration
        $this->registerRoute($domain, $routeSlug);

        CLI::write('');
        CLI::write(sprintf('>>> Domain [%s] created successfully conforming to Golden Domain Template! <<<', $domain), 'green');

        return EXIT_SUCCESS;
    }

    /**
     * Helper Idempotent Route Registration
     */
    protected function registerRoute(string $domain, string $routeSlug): void
    {
        $routesPath = APPPATH . 'Config/Routes.php';
        if (!file_exists($routesPath)) {
            return;
        }

        $routesContent = file_get_contents($routesPath);

        $routeBlock = sprintf(
            "\n// Load Domain %s Routes\nif (file_exists(APPPATH . 'Domains/%s/Routes/%s.php')) {\n    require APPPATH . 'Domains/%s/Routes/%s.php';\n}\n",
            $domain,
            $domain,
            $routeSlug,
            $domain,
            $routeSlug
        );

        $checkString = sprintf("Domains/%s/Routes/%s.php", $domain, $routeSlug);

        if (str_contains($routesContent, $checkString)) {
            CLI::write(sprintf('  [SKIP] Route for [%s] already registered in Config/Routes.php', $domain), 'yellow');
        } else {
            file_put_contents($routesPath, $routeBlock, FILE_APPEND);
            CLI::write(sprintf('  [REGISTERED] Route for [%s] in Config/Routes.php', $domain), 'green');
        }
    }
}
