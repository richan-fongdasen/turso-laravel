<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Event;
use PDO;
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

        if (config('database.default') !== 'turso') {
            return;
        }

        Event::listen(function (CommandStarting $event) {
            if (! app()->bound('running-artisan-command')) {
                app()->instance('running-artisan-command', data_get($event, 'command'));
            }

            Turso::disableReadReplica();
        });

        Event::listen(function (CommandFinished $event) {
            if (data_get($event, 'command') === 'turso:sync') {
                return;
            }

            if (
                app()->bound(TursoConnection::class) &&
                app(TursoConnection::class)->hasUpdated() &&
                (app('running-artisan-command') === data_get($event, 'command'))
            ) {
                Turso::sync();
                Turso::enableReadReplica();
            }
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
            return new TursoManager(config('database.connections.turso', []));
        });

        $this->app->extend(DatabaseManager::class, function (DatabaseManager $manager) {
            Connection::resolverFor('turso', function ($connection = null, ?string $database = null, string $prefix = '', array $config = []) {
                $connector = new TursoConnector();
                $pdo = $connector->connect($config);

                $connection = new TursoConnection($pdo, $database ?? 'turso', $prefix, $config);
                app()->instance(TursoConnection::class, $connection);

                $replicaPath = (string) data_get($config, 'db_replica');

                if (($replicaPath !== '') && file_exists($replicaPath)) {
                    $connection->setReadPdo(new PDO('sqlite:' . $replicaPath));
                }

                return $connection;
            });

            return $manager;
        });
    }
}
