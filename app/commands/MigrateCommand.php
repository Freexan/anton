<?php

declare(strict_types=1);

namespace flight\commands;

use flight\database\PdoWrapper;

class MigrateCommand extends AbstractBaseCommand
{
    public function __construct(array $config)
    {
        parent::__construct('db:migrate', 'Run SQL migrations on SQLite database.', $config);
    }

    public function execute()
    {
        $io = $this->app()->io();
        $dbFile = __DIR__ . '/../database.sqlite';
        $dsn = 'sqlite:' . $dbFile;
        $pdo = new PdoWrapper($dsn);

        $rootMigrations = __DIR__ . '/../../migrations';
        $appMigrations = __DIR__ . '/../migrations';
        $migrationsDir = is_dir($rootMigrations) ? $rootMigrations : (is_dir($appMigrations) ? $appMigrations : null);
        if ($migrationsDir === null) {
            $io->warn('No migrations directory found.', true);
            return;
        }

        $files = glob($migrationsDir . '/*.sql') ?: [];
        sort($files);
        if (empty($files)) {
            $io->warn('No .sql migration files found.', true);
            return;
        }

        $io->info('Using database file: ' . $dbFile);
        foreach ($files as $file) {
            $io->info('Applying: ' . basename($file));
            $sql = file_get_contents($file) ?: '';
            if ($sql === '') {
                $io->warn('Skipped empty file: ' . $file);
                continue;
            }
            $pdo->exec($sql);
        }
        $io->ok('Migrations applied.', true);
    }
}
