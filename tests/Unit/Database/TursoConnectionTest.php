<?php

use Illuminate\Support\Facades\DB;
use RichanFongdasen\Turso\Database\TursoPDO;

beforeEach(function () {
    $this->connection = DB::connection('turso');
});

test('it can create a PDO object for read replica database connection', function () {
    expect($this->connection->getReadPdo())->toBeInstanceOf(TursoPDO::class);

    $pdo = $this->connection->createReadPdo([
        'db_replica' => '/dev/null',
    ]);

    expect($pdo)->toBeInstanceOf(\PDO::class)
        ->and($this->connection->getReadPdo())->toBe($pdo);
})->group('TursoConnectionTest', 'UnitTest');

test('it will return null when trying to create read PDO with no replica database path configured', function () {
    expect($this->connection->createReadPdo())->toBeNull();
})->group('TursoConnectionTest', 'UnitTest');

test('it can escape binary data and convert it into string type', function () {
    $actual = $this->connection->escape('Hello world!', true);

    expect($actual)->toBe("x'48656c6c6f20776f726c6421'");
})->group('TursoConnectionTest', 'UnitTest');
