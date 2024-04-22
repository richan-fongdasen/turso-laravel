<?php

use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->pdo = DB::connection()->getPdo();

    $this->pdo->exec('CREATE TABLE "projects" ("id" INTEGER PRIMARY KEY, "name" TEXT);');
});

afterEach(function () {
    $this->pdo->exec('DROP TABLE IF EXISTS "projects";');
});

test('it can execute SQL command', function () {
    expect($this->pdo->exec('PRAGMA foreign_keys = ON;'))->toBe(0);
})->group('TursoPDOTest', 'FeatureTest');

test('it can begin the database transaction, and rollback the changes.', function () {
    $this->pdo->beginTransaction();

    $this->pdo->exec('INSERT INTO "projects" ("name") VALUES (\'Project 1\');');
    $this->pdo->exec('INSERT INTO "projects" ("name") VALUES (\'Project 2\');');

    expect($this->pdo->inTransaction())->toBeTrue()
        ->and($this->pdo->exec('SELECT * FROM "projects";'))->toBe(2);

    $this->pdo->rollBack();

    expect($this->pdo->inTransaction())->toBeFalse()
        ->and($this->pdo->exec('SELECT * FROM "projects";'))->toBe(0);
})->group('TursoPDOTest', 'FeatureTest');

test('it can begin the database transaction, and commit the changes.', function () {
    $this->pdo->beginTransaction();

    $this->pdo->exec('INSERT INTO "projects" ("name") VALUES (\'Project 1\');');
    $this->pdo->exec('INSERT INTO "projects" ("name") VALUES (\'Project 2\');');

    expect($this->pdo->inTransaction())->toBeTrue()
        ->and($this->pdo->exec('SELECT * FROM "projects";'))->toBe(2);

    $this->pdo->commit();

    expect($this->pdo->inTransaction())->toBeFalse()
        ->and($this->pdo->exec('SELECT * FROM "projects";'))->toBe(2);
})->group('TursoPDOTest', 'FeatureTest');
