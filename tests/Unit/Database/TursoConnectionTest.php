<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use RichanFongdasen\Turso\Database\TursoPDO;
use RichanFongdasen\Turso\Jobs\TursoSyncJob;

test('it can create a PDO object for read replica database connection', function () {
    expect(DB::connection('turso')->getReadPdo())->toBeInstanceOf(TursoPDO::class);

    $pdo = DB::connection('turso')->createReadPdo([
        'db_replica' => '/dev/null',
    ]);

    expect($pdo)->toBeInstanceOf(\PDO::class)
        ->and(DB::connection('turso')->getReadPdo())->toBe($pdo);
})->group('TursoConnectionTest', 'UnitTest');

test('it will return null when trying to create read PDO with no replica database path configured', function () {
    expect(DB::connection('turso')->createReadPdo())->toBeNull();
})->group('TursoConnectionTest', 'UnitTest');

test('it can escape binary data and convert it into string type', function () {
    $actual = DB::connection('turso')->escape('Hello world!', true);

    expect($actual)->toBe("x'48656c6c6f20776f726c6421'");
})->group('TursoConnectionTest', 'UnitTest');

test('it can trigger the sync command to synchronize the database', function () {
    Process::fake();

    config([
        'database.connections.turso.db_replica' => '/tmp/turso.sqlite',
        'turso-laravel.sync_command.node_path'  => '/dev/null',
    ]);

    DB::connection('turso')->sync();

    Process::assertRan(function (PendingProcess $process) {
        $expectedPath = realpath(__DIR__ . '/../../..');

        expect($process->command)->toBe('/dev/null turso-sync.mjs "http://127.0.0.1:8080" "your-access-token" "/tmp/turso.sqlite"')
            ->and($process->timeout)->toBe(60)
            ->and($process->path)->toBe($expectedPath);

        return true;
    });
})->group('TursoConnectionTest', 'UnitTest');

test('it can dispatch the sync job to synchronize the database', function () {
    Bus::fake();

    config([
        'database.connections.turso.db_replica' => '/tmp/turso.sqlite',
        'turso-laravel.sync_command.node_path'  => '/dev/null',
    ]);

    DB::connection('turso')->backgroundSync();

    Bus::assertDispatched(TursoSyncJob::class);
})->group('TursoConnectionTest', 'UnitTest');

test('it can enable query logging feature', function () {
    DB::connection('turso')->enableQueryLog();

    expect(DB::connection('turso')->logging())->toBeTrue()
        ->and(DB::connection('turso')->tursoPdo()->getClient()->logging())->toBeTrue();
})->group('TursoConnectionTest', 'UnitTest');

test('it can disable query logging feature', function () {
    DB::connection('turso')->disableQueryLog();

    expect(DB::connection('turso')->logging())->toBeFalse()
        ->and(DB::connection('turso')->tursoPdo()->getClient()->logging())->toBeFalse();
})->group('TursoConnectionTest', 'UnitTest');

test('it can get the query log', function () {
    DB::connection('turso')->enableQueryLog();

    $log = DB::connection('turso')->getQueryLog();

    expect($log)->toBeArray()
        ->and($log)->toHaveCount(0);
})->group('TursoConnectionTest', 'UnitTest');

test('it can flush the query log', function () {
    DB::connection('turso')->enableQueryLog();

    DB::connection('turso')->flushQueryLog();

    $log = DB::connection('turso')->getQueryLog();

    expect($log)->toBeArray()
        ->and($log)->toHaveCount(0);
})->group('TursoConnectionTest', 'UnitTest');

test('it will replace the libsql protocol in database url to be https protocol', function () {
    config([
        'database.connections.turso.db_url' => 'libsql://project-name.turso.io',
    ]);

    expect(DB::connection('turso')->getConfig('db_url'))->toBe('https://project-name.turso.io');
})->group('TursoConnectionTest', 'UnitTest');
