<?php

use Illuminate\Support\Facades\DB;
use RichanFongdasen\Turso\Database\TursoPDO;
use RichanFongdasen\Turso\Exceptions\FeatureNotSupportedException;

beforeEach(function () {
    $this->pdo = DB::connection()->getPdo();
});

test('it should be an instance of TursoPDO', function () {
    expect($this->pdo)->toBeInstanceOf(TursoPDO::class);
})->group('TursoPDOTest', 'UnitTest');

test('it raises exception on beginning a database transaction', function () {
    $this->pdo->beginTransaction();
})->throws(FeatureNotSupportedException::class)->group('TursoPDOTest', 'UnitTest');

test('it raises exception on committing a database transaction', function () {
    $this->pdo->commit();
})->throws(FeatureNotSupportedException::class)->group('TursoPDOTest', 'UnitTest');

test('it raises exception on rolling back a database transaction', function () {
    $this->pdo->rollBack();
})->throws(FeatureNotSupportedException::class)->group('TursoPDOTest', 'UnitTest');

test('database transaction status should always be false', function () {
    expect($this->pdo->inTransaction())->toBeFalse();
})->group('TursoPDOTest', 'UnitTest');

test('it can manage the last insert id value', function () {
    $this->pdo->setLastInsertId(value: 123);

    expect($this->pdo->lastInsertId())->toBe('123');
})->group('TursoPDOTest', 'UnitTest');
