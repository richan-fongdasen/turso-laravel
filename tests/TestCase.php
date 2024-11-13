<?php

namespace RichanFongdasen\Turso\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use RichanFongdasen\Turso\TursoLaravelServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'RichanFongdasen\\Turso\\Tests\\Fixtures\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            TursoLaravelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.connections.turso', [
            'driver'                  => 'turso',
            'db_url'                  => env('DB_URL', 'http://127.0.0.1:8080'),
            'db_replica'              => env('DB_REPLICA'),
            'prefix'                  => env('DB_PREFIX', ''),
            'access_token'            => 'your-access-token',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'sticky'                  => env('DB_STICKY', true),
        ]);
        config()->set('database.default', 'turso');
        config()->set('queue.default', 'sync');
    }
}
