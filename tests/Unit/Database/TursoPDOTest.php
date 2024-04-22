<?php

use Illuminate\Support\Facades\DB;
use RichanFongdasen\Turso\Database\TursoPDO;

beforeEach(function () {
    $this->pdo = DB::connection()->getPdo();
});

test('it should be an instance of TursoPDO', function () {
    expect($this->pdo)->toBeInstanceOf(TursoPDO::class);
})->group('TursoPDOTest', 'UnitTest');

test('it can manage the last insert id value', function () {
    $this->pdo->setLastInsertId(value: 123);

    expect($this->pdo->lastInsertId())->toBe('123');
})->group('TursoPDOTest', 'UnitTest');
