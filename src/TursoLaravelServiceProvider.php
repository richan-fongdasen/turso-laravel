<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use Illuminate\Support\Facades\DB;
use RichanFongdasen\Turso\Commands\TursoSyncCommand;
use RichanFongdasen\Turso\Database\TursoConnection;
use RichanFongdasen\Turso\Database\TursoConnector;
use RichanFongdasen\Turso\Facades\Turso;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TursoLaravelServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        app()->terminating(function () {
            Turso::sync();
        });
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('turso-laravel')
            ->hasConfigFile()
            ->hasCommand(TursoSyncCommand::class);

        $this->publishes([
            realpath(dirname(__DIR__) . '/turso-sync.mjs') => base_path('turso-sync.mjs'),
        ], 'sync-script');
    }

    public function register(): void
    {
        parent::register();

        $this->app->scoped(TursoManager::class, function () {
            return new TursoManager();
        });

        DB::extend('turso', function (array $config, string $name) {
            $config['database'] = null;
            $config['name'] = $name;

            $connector = new TursoConnector();
            $pdo = $connector->connect($config);

            $connection = new TursoConnection($pdo, $config['name'], $config['prefix'], $config);
            $connection->createReadPdo($config);

            return $connection;
        });
    }
}
