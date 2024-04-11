<?php

use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->pdo = DB::connection()->getPdo();
});

test('it can execute SQL command', function () {
    expect($this->pdo->exec('PRAGMA foreign_keys = ON;'))->toBe(0);
})->group('TursoPDOTest', 'FeatureTest');
