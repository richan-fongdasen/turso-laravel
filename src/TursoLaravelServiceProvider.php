<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use RichanFongdasen\Turso\Database\TursoConnection;
use RichanFongdasen\Turso\Database\TursoConnector;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TursoLaravelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('turso-laravel')
            ->hasConfigFile();
    }

    public function register(): void
    {
        parent::register();

        $this->app->scoped(TursoClient::class, function () {
            return new TursoClient();
        });

        $this->app->extend(DatabaseManager::class, function (DatabaseManager $manager) {
            Connection::resolverFor('turso', function ($connection = null, ?string $database = null, string $prefix = '', array $config = []) {
                $connector = new TursoConnector();
                $pdo = $connector->connect($config);

                return new TursoConnection($pdo, $database ?? ':memory:', $prefix, $config);
            });

            return $manager;
        });
    }
}
