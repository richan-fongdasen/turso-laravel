<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

beforeEach(function () {
    migrateTables('users');
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can insert a single record', function () {
    $result = DB::table('users')->insert([
        'name'  => 'John Doe',
        'email' => 'john.doe@gmail.com',
    ]);

    $user = DB::table('users')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('users')->count())->toBe(1)
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john.doe@gmail.com');
})->group('InsertStatementsTest', 'QueryBuilder', 'FeatureTest');

test('it can insert multiple records', function () {
    $result = DB::table('users')->insert([
        [
            'name'  => 'John Doe',
            'email' => 'john.doe@gmail.com',
        ],
        [
            'name'  => 'June Monroe',
            'email' => 'june.monroe@gmail.com',
        ],
    ]);

    $users = DB::table('users')->get();

    expect($result)->toBeTrue()
        ->and(DB::table('users')->count())->toBe(2)
        ->and($users->first()->name)->toBe('John Doe')
        ->and($users->first()->email)->toBe('john.doe@gmail.com')
        ->and($users->last()->name)->toBe('June Monroe')
        ->and($users->last()->email)->toBe('june.monroe@gmail.com');
})->group('InsertStatementsTest', 'QueryBuilder', 'FeatureTest');

test('it can get the auto increment id as the result of insert command', function () {
    User::factory()->create();

    $expectation = User::factory()->make();

    $result = DB::table('users')->insertGetId([
        'name'  => $expectation->name,
        'email' => $expectation->email,
    ]);

    $newUser = DB::table('users')->find($result);

    expect(DB::table('users')->count())->toBe(2)
        ->and($result)->toBe(2)
        ->and($newUser->name)->toBe($expectation->name)
        ->and($newUser->email)->toBe($expectation->email);
})->group('InsertStatementsTest', 'QueryBuilder', 'FeatureTest');
