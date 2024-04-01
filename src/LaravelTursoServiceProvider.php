<?php

declare(strict_types=1);

namespace RichanFongdasen\LaravelTurso;

use RichanFongdasen\LaravelTurso\Commands\LaravelTursoCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelTursoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-turso')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-turso_table')
            ->hasCommand(LaravelTursoCommand::class);
    }
}
