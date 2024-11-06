<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

test('it will fail if the specified connection is not a Turso connection', function () {
    Process::fake();

    $result = Artisan::call('turso:sync', ['connectionName' => 'mysql']);

    expect($result)->toBe(1);
    expect(Artisan::output())->toContain('The specified connection is not a Turso connection.');
})->group('TursoSyncCommandTest', 'UnitTest');

test('it will fail if the specified connection does not have a read replica', function () {
    Process::fake();

    $result = Artisan::call('turso:sync', ['connectionName' => 'turso']);

    expect($result)->toBe(1);
    expect(Artisan::output())->toContain('The specified connection does not have a read replica.');
})->group('TursoSyncCommandTest', 'UnitTest');

test('it can run the cli script to sync the database', function () {
    Process::fake();

    config([
        'database.connections.turso.db_replica' => '/tmp/turso.sqlite',
        'turso-laravel.sync_command.node_path'  => '/dev/null',
    ]);

    Artisan::call('turso:sync');

    Process::assertRan(function (PendingProcess $process) {
        $expectedPath = realpath(__DIR__ . '/../../..');

        expect($process->command)->toBe('/dev/null turso-sync.mjs "http://127.0.0.1:8080" "your-access-token" "/tmp/turso.sqlite"')
            ->and($process->timeout)->toBe(60)
            ->and($process->path)->toBe($expectedPath);

        return true;
    });
})->group('TursoSyncCommandTest', 'UnitTest');

test('it can handle process error output', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Whooops! Something went wrong!',
            errorOutput: 'Error: Something went wrong!',
            exitCode: 500
        ),
    ]);

    config([
        'database.connections.turso.db_replica' => '/tmp/turso.sqlite',
        'turso-laravel.sync_command.node_path'  => '/dev/null',
    ]);

    $result = Artisan::call('turso:sync');
})->throws(RuntimeException::class)->group('TursoSyncCommandTest', 'UnitTest');

test('it raises exception on failing to find node executable file', function () {
    config([
        'database.connections.turso.db_replica' => '/tmp/turso.sqlite',
        'turso-laravel.sync_command.node_path'  => '/usr/invalid/bin/node',
    ]);

    $result = Artisan::call('turso:sync');
})->throws(RuntimeException::class)->group('TursoSyncCommandTest', 'UnitTest');
