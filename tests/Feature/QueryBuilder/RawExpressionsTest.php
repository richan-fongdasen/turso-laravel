<?php

use Illuminate\Support\Carbon;
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

test('it can perform raw column selection', function () {
    $result = DB::table('users')
        ->select(DB::raw('count (*) as user_count'))
        ->get();

    expect($result->first()->user_count)->toBe(3);
})->group('RawExpressionsTest', 'QueryBuilder', 'FeatureTest');

test('it can perform selectRaw query', function () {
    $result = DB::table('users')
        ->selectRaw('id, id * ? as multiplied_id', [3])
        ->orderBy('id')
        ->get();

    expect($result->count())->toBe(3)
        ->and($result[0]->multiplied_id)->toBe((int) $this->user1->getKey() * 3)
        ->and($result[1]->multiplied_id)->toBe((int) $this->user2->getKey() * 3)
        ->and($result[2]->multiplied_id)->toBe((int) $this->user3->getKey() * 3);
})->group('RawExpressionsTest', 'QueryBuilder', 'FeatureTest');

test('it can perform whereRaw query', function () {
    $newUser = User::factory()->create([
        'created_at' => Carbon::parse('1945-08-17 00:00:00'),
    ]);

    $selectedUser = DB::table('users')
        ->whereRaw("strftime('%Y-%m', created_at) = '1945-08'")
        ->first();

    expect($selectedUser)->not->toBeNull()
        ->and($selectedUser->id)->toBe($newUser->id)
        ->and($selectedUser->name)->toBe($newUser->name);
})->group('RawExpressionsTest', 'QueryBuilder', 'FeatureTest');

test('it can perform orderByRaw query', function () {
    $newUser = User::factory()->create([
        'created_at' => Carbon::parse('1945-08-17 00:00:00'),
    ]);

    $result = DB::table('users')
        ->orderByRaw('updated_at - created_at DESC')
        ->first();

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($newUser->id)
        ->and($result->name)->toBe($newUser->name);
})->group('RawExpressionsTest', 'QueryBuilder', 'FeatureTest');
