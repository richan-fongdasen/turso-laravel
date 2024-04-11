<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

beforeEach(function () {
    migrateTables('users');

    $this->user1 = User::factory()->create();
    $this->user2 = User::factory()->create();
    $this->user3 = User::factory()->create();
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can delete multiple records', function () {
    DB::table('users')->where('id', '>', 1)->delete();

    expect(DB::table('users')->count())->toBe(1);
})->group('DeleteStatementsTest', 'QueryBuilder', 'FeatureTest');

test('it can truncate the whole table content', function () {
    DB::table('users')->truncate();

    expect(DB::table('users')->count())->toBe(0);
})->group('DeleteStatementsTest', 'QueryBuilder', 'FeatureTest');

test('it can delete a single record', function () {
    DB::table('users')->where('id', $this->user2->getKey())->delete();

    expect(DB::table('users')->count())->toBe(2);
})->group('DeleteStatementsTest', 'QueryBuilder', 'FeatureTest');

test('it can delete all records', function () {
    DB::table('users')->delete();

    expect(DB::table('users')->count())->toBe(0);
})->group('DeleteStatementsTest', 'QueryBuilder', 'FeatureTest');
