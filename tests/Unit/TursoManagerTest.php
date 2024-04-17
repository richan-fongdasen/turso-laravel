<?php

use Illuminate\Http\Client\Request;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use RichanFongdasen\Turso\Facades\Turso;
use RichanFongdasen\Turso\Jobs\TursoSyncJob;

test('it returns false on enabling read replica when read replica is not configured', function () {
    expect(Turso::enableReadReplica())->toBeFalse();
})->group('TursoManagerTest', 'UnitTest');

test('it can disable the read replica database connection', function () {
    DB::connection('turso')->setReadPdo(new \PDO('sqlite::memory:'));

    Http::fake();

    Turso::disableReadReplica();
    Turso::resetClientState();

    Turso::query('SELECT * FROM sqlite_master');

    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe('http://127.0.0.1:8080/v3/pipeline')
            ->and($request->data())->toBe([
                'requests' => [[
                    'type' => 'execute',
                    'stmt' => [
                        'sql' => 'SELECT * FROM sqlite_master',
                    ],
                ]],
            ]);

        return true;
    });
})->group('TursoManagerTest', 'UnitTest');

test('it can reenable the read replica database connection', function () {
    Turso::disableReadReplica();

    expect(Turso::enableReadReplica())->toBeTrue();
})->group('TursoManagerTest', 'UnitTest');

test('it raises exception on calling an undefined method', function () {
    Turso::undefinedMethod();
})->throws(\BadMethodCallException::class)->group('TursoManagerTest', 'UnitTest');

test('it raises exception on calling the sync() method without configuring the read replica', function () {
    Turso::sync();
})->throws(\LogicException::class)->group('TursoManagerTest', 'UnitTest');

test('it raises exception on calling the backgroundSync() method without configuring the read replica', function () {
    Turso::backgroundSync();
})->throws(\LogicException::class)->group('TursoManagerTest', 'UnitTest');

test('it can trigger the sync command immediately', function () {
    Process::fake();

    config([
        'database.connections.turso.db_replica' => '/tmp/turso.sqlite',
        'turso-laravel.sync_command.node_path'  => '/dev/null',
    ]);

    Turso::sync();

    Process::assertRan(function (PendingProcess $process) {
        $expectedPath = realpath(__DIR__ . '/../..');

        expect($process->command)->toBe('/dev/null turso-sync.mjs "http://127.0.0.1:8080" "your-access-token" "/tmp/turso.sqlite"')
            ->and($process->timeout)->toBe(60)
            ->and($process->path)->toBe($expectedPath);

        return true;
    });
})->group('TursoManagerTest', 'UnitTest');

test('it can dispatch the sync background job', function () {
    Bus::fake();

    config(['database.connections.turso.db_replica' => '/tmp/turso.sqlite']);

    Turso::backgroundSync();

    Bus::assertDispatched(TursoSyncJob::class);
})->group('TursoManagerTest', 'UnitTest');

test('it can run the sync background job and call the sync artisan command', function () {
    Process::fake();

    config([
        'database.connections.turso.db_replica' => '/tmp/turso.sqlite',
        'turso-laravel.sync_command.node_path'  => '/dev/null',
    ]);

    Turso::backgroundSync();

    Process::assertRan(function (PendingProcess $process) {
        $expectedPath = realpath(__DIR__ . '/../..');

        expect($process->command)->toBe('/dev/null turso-sync.mjs "http://127.0.0.1:8080" "your-access-token" "/tmp/turso.sqlite"')
            ->and($process->timeout)->toBe(60)
            ->and($process->path)->toBe($expectedPath);

        return true;
    });
})->group('TursoManagerTest', 'UnitTest');
