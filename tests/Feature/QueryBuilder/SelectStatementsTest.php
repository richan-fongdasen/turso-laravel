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

test('it can specify a select clause', function () {
    $users = DB::table('users')->select('name', 'email as user_email')->get();

    expect($users)->toHaveCount(3)
        ->and($users[0]->name)->toEqual($this->user1->name)
        ->and($users[0]->user_email)->toEqual($this->user1->email)
        ->and($users[1]->name)->toEqual($this->user2->name)
        ->and($users[1]->user_email)->toEqual($this->user2->email)
        ->and($users[2]->name)->toEqual($this->user3->name)
        ->and($users[2]->user_email)->toEqual($this->user3->email);
})->group('SelectStatementsTest', 'QueryBuilder', 'FeatureTest');

test('it can return distinct result', function () {
    $newUser = User::factory()->create([
        'name' => $this->user2->name,
    ]);

    $users = DB::table('users')->select('name')->distinct()->get();

    expect($users)->toHaveCount(3)
        ->and(DB::table('users')->count())->toEqual(4)
        ->and($users[0]->name)->toEqual($this->user1->name)
        ->and($users[1]->name)->toEqual($this->user2->name)
        ->and($users[2]->name)->toEqual($this->user3->name);
})->group('SelectStatementsTest', 'QueryBuilder', 'FeatureTest');

test('it can add another column selection', function () {
    $query = DB::table('users')->select('name');

    $users = $query->addSelect('email')->get();

    expect($users)->toHaveCount(3)
        ->and($users[0]->name)->toEqual($this->user1->name)
        ->and($users[0]->email)->toEqual($this->user1->email)
        ->and($users[1]->name)->toEqual($this->user2->name)
        ->and($users[1]->email)->toEqual($this->user2->email)
        ->and($users[2]->name)->toEqual($this->user3->name)
        ->and($users[2]->email)->toEqual($this->user3->email);
})->group('SelectStatementsTest', 'QueryBuilder', 'FeatureTest');
