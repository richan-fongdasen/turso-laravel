<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

beforeEach(function () {
    migrateTables('users', 'posts');

    $this->user = User::factory()->create();
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can rollback the transaction', function () {
    $this->user->name = 'John Doe';
    $this->user->save();

    expect(User::count())->toBe(1);

    DB::transaction(function () {
        $this->user->name = 'Jane Doe';
        $this->user->save();

        expect(User::first()->name)->toBe('Jane Doe');

        DB::rollBack();
    });

    expect(User::count())->toBe(1);
    expect(User::first()->name)->toBe('John Doe');
})->group('DatabaseTransactionsTest', 'FeatureTest');

test('it can rollback the transaction by manually using the transactions', function () {
    $this->user->name = 'John Doe';
    $this->user->save();

    expect(User::count())->toBe(1);

    DB::beginTransaction();

    $this->user->name = 'Jane Doe';
    $this->user->save();

    expect(User::first()->name)->toBe('Jane Doe');

    DB::rollBack();

    expect(User::count())->toBe(1);
    expect(User::first()->name)->toBe('John Doe');
})->group('DatabaseTransactionsTest', 'FeatureTest');

test('it can commit the transaction', function () {
    $this->user->name = 'John Doe';
    $this->user->save();

    expect(User::count())->toBe(1);

    DB::transaction(function () {
        $this->user->name = 'Jane Doe';
        $this->user->save();

        expect(User::first()->name)->toBe('Jane Doe');
    });

    expect(User::count())->toBe(1);
    expect(User::first()->name)->toBe('Jane Doe');
})->group('DatabaseTransactionsTest', 'FeatureTest');

test('it can commit the transaction by manually using the transactions', function () {
    $this->user->name = 'John Doe';
    $this->user->save();

    expect(User::count())->toBe(1);

    DB::beginTransaction();

    $this->user->name = 'Jane Doe';
    $this->user->save();

    expect(User::first()->name)->toBe('Jane Doe');

    DB::commit();

    expect(User::count())->toBe(1);
    expect(User::first()->name)->toBe('Jane Doe');
})->group('DatabaseTransactionsTest', 'FeatureTest');
