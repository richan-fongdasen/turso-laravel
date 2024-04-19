<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

beforeEach(function () {
    migrateTables('users');

    $this->user = User::factory()->create();
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can update the user\'s email address', function () {
    DB::table('users')
        ->where('id', $this->user->getKey())
        ->update([
            'email' => 'richan.fongdasen@gmail.com',
        ]);

    $updatedUser = DB::table('users')->find($this->user->getKey());

    expect($updatedUser->email)->toBe('richan.fongdasen@gmail.com');
})->group('UpdateStatementTest', 'QueryBuilder', 'FeatureTest');

test('it can insert a new record with updateOrInsert method', function () {
    DB::table('users')
        ->updateOrInsert(
            [
                'name'  => 'John Doe',
                'email' => 'john.doe@gmail.com',
            ],
            [
                'remember_token' => '1234567890',
            ]
        );

    $user = DB::table('users')
        ->where('name', 'John Doe')
        ->where('email', 'john.doe@gmail.com')
        ->first();

    expect($user->id)->toBe(2)
        ->and($user->remember_token)->toBe('1234567890');
})->group('UpdateStatementTest', 'QueryBuilder', 'FeatureTest');

test('it can update an existing record with updateOrInsert method', function () {
    DB::table('users')
        ->updateOrInsert(
            [
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ],
            [
                'remember_token' => '1234567890',
            ]
        );

    $updatedUser = DB::table('users')->find($this->user->getKey());

    expect(DB::hasModifiedRecords())->toBeTrue()
        ->and(DB::table('users')->count())->toBe(1)
        ->and($updatedUser->remember_token)->toBe('1234567890');
})->group('UpdateStatementTest', 'QueryBuilder', 'FeatureTest');

test('it can increment and decrement a column value', function () {
    DB::table('users')->increment('id', 5);

    expect(DB::table('users')->first()->id)->toBe(6);

    DB::table('users')->decrement('id', 3);

    expect(DB::table('users')->first()->id)->toBe(3);
})->group('UpdateStatementTest', 'QueryBuilder', 'FeatureTest');
