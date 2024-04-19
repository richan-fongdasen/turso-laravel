<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    DB::statement('CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null)');

    $this->connection = DB::connection();
    $this->pdo = $this->connection->getPdo();
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can fetch all row sets of a simple select query result in associative array format', function () {
    $expectation = [
        [
            'type'     => 'table',
            'name'     => 'migrations',
            'tbl_name' => 'migrations',
            'sql'      => 'CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null)',
        ],
    ];

    $statement = $this->pdo->prepare('SELECT * FROM sqlite_schema WHERE type = ? AND name NOT LIKE ?');
    $this->connection->bindValues($statement, $this->connection->prepareBindings(['table', 'sqlite_%']));

    $statement->execute();

    $statement->setFetchMode(\PDO::FETCH_ASSOC);
    $response = $statement->fetchAll();

    expect(count($response))->toBe(1)
        ->and($response[0]['type'])->toBe($expectation[0]['type'])
        ->and($response[0]['name'])->toBe($expectation[0]['name'])
        ->and($response[0]['tbl_name'])->toBe($expectation[0]['tbl_name'])
        ->and($response[0]['sql'])->toBe($expectation[0]['sql']);
})->group('TursoPDOStatementTest', 'FeatureTest');

test('it can fetch all row sets of a simple select query result in both array format', function () {
    $expectation = [
        [
            'type'     => 'table',
            'name'     => 'migrations',
            'tbl_name' => 'migrations',
            'sql'      => 'CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null)',
            0          => 'table',
            1          => 'migrations',
            2          => 'migrations',
            4          => 'CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null)',
        ],
    ];

    $statement = $this->pdo->prepare('SELECT * FROM sqlite_schema WHERE type = ? AND name NOT LIKE ?');
    $this->connection->bindValues($statement, $this->connection->prepareBindings(['table', 'sqlite_%']));

    $statement->execute();

    $statement->setFetchMode(\PDO::FETCH_BOTH);
    $response = $statement->fetchAll();

    expect(count($response))->toBe(1)
        ->and($response[0]['type'])->toBe($expectation[0]['type'])
        ->and($response[0]['name'])->toBe($expectation[0]['name'])
        ->and($response[0]['tbl_name'])->toBe($expectation[0]['tbl_name'])
        ->and($response[0]['sql'])->toBe($expectation[0]['sql'])
        ->and($response[0][0])->toBe($expectation[0][0])
        ->and($response[0][1])->toBe($expectation[0][1])
        ->and($response[0][2])->toBe($expectation[0][2])
        ->and($response[0][4])->toBe($expectation[0][4]);
})->group('TursoPDOStatementTest', 'FeatureTest');

test('it can fetch each row set of a simple select query result in associative array format', function () {
    $expectation = [
        'type'     => 'table',
        'name'     => 'migrations',
        'tbl_name' => 'migrations',
        'sql'      => 'CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null)',
    ];

    $statement = $this->pdo->prepare('SELECT * FROM sqlite_schema WHERE type = ? AND name NOT LIKE ?');
    $this->connection->bindValues($statement, $this->connection->prepareBindings(['table', 'sqlite_%']));

    $statement->execute();

    $statement->setFetchMode(\PDO::FETCH_ASSOC);
    $response = $statement->fetch();

    expect($response['type'])->toBe($expectation['type'])
        ->and($response['name'])->toBe($expectation['name'])
        ->and($response['tbl_name'])->toBe($expectation['tbl_name'])
        ->and($response['sql'])->toBe($expectation['sql'])
        ->and($statement->fetch())->toBeFalse();
})->group('TursoPDOStatementTest', 'FeatureTest');

test('it can count the rows of query result set', function () {
    $statement = $this->pdo->prepare('SELECT * FROM sqlite_schema WHERE type = ? AND name NOT LIKE ?');
    $this->connection->bindValues($statement, $this->connection->prepareBindings(['table', 'sqlite_%']));

    $statement->execute();

    expect($statement->rowCount())->toBe(1);
})->group('TursoPDOStatementTest', 'FeatureTest');

test('it can perform query execution with binding values', function () {
    DB::statement('INSERT INTO "migrations" ("migration", "batch") VALUES (?, ?)', ['CreateUsersTable', 1]);

    $statement = $this->pdo->prepare('SELECT * FROM "migrations" WHERE "migration" = ? AND "batch" = ?');
    $statement->execute(['CreateUsersTable', 1]);

    expect($statement->rowCount())->toBe(1);

    $result = $statement->fetch();

    expect($result['migration'])->toBe('CreateUsersTable')
        ->and($result['batch'])->toBe(1);
})->group('TursoPDOStatementTest', 'FeatureTest');

test('it can perform update statement with binding values', function () {
    DB::statement('INSERT INTO "migrations" ("migration", "batch") VALUES (?, ?)', ['CreateUsersTable', 1]);

    $statement = $this->pdo->prepare('UPDATE "migrations" SET "migration" = ? WHERE "id" = ?');
    $statement->execute(['CreateRolesTable', 1]);

    expect($statement->rowCount())->toBe(1);
})->group('TursoPDOStatementTest', 'FeatureTest');
