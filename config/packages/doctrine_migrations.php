<?php

use Symfony\Config\DoctrineMigrationsConfig;

return static function (DoctrineMigrationsConfig $migrationsConfig) {
    $migrationsConfig->migrationsPath('DoctrineMigrations', '%kernel.project_dir%/migrations')
        ->enableProfiler('%kernel.debug%');
};
