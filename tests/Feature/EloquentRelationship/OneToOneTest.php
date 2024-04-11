<?php

use RichanFongdasen\Turso\Tests\Fixtures\Models\Phone;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

beforeEach(function () {
    migrateTables('users', 'phones');

    $this->user = User::factory()->create();
    $this->phone = Phone::factory()->create([
        'user_id' => $this->user->getKey(),
    ]);
});

afterEach(function () {
    DB::getSchemaBuilder()->dropAllTables();
});

test('it can retrieve the related model in one to one relationship', function () {
    $user = User::findOrFail($this->user->getKey());
    $phone = $user->phone;

    expect($phone)->not->toBeNull()
        ->and($phone->getKey())->toBe($this->phone->getKey())
        ->and($phone->user->getKey())->toBe($this->user->getKey())
        ->and($phone->phone_number)->toBe($this->phone->phone_number);
})->group('OneToOneTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in one to one relationship using eager loading', function () {
    $user = User::with('phone')->findOrFail($this->user->getKey());
    $phone = $user->phone;

    expect($phone)->not->toBeNull()
        ->and($phone->getKey())->toBe($this->phone->getKey())
        ->and($phone->user->getKey())->toBe($this->user->getKey())
        ->and($phone->phone_number)->toBe($this->phone->phone_number);
})->group('OneToOneTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in inverted way of one to one relationship', function () {
    $phone = Phone::findOrFail($this->phone->getKey());
    $user = $phone->user;

    expect($user)->not->toBeNull()
        ->and($user->getKey())->toBe($this->user->getKey())
        ->and($user->name)->toBe($this->user->name)
        ->and($user->email)->toBe($this->user->email)
        ->and($user->email_verified_at->format('Y-m-d H:i:s'))->toBe($this->user->email_verified_at->format('Y-m-d H:i:s'));
})->group('OneToOneTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in inverted way of one to one relationship using eager loading', function () {
    $phone = Phone::with('user')->findOrFail($this->phone->getKey());
    $user = $phone->user;

    expect($user)->not->toBeNull()
        ->and($user->getKey())->toBe($this->user->getKey())
        ->and($user->name)->toBe($this->user->name)
        ->and($user->email)->toBe($this->user->email)
        ->and($user->email_verified_at->format('Y-m-d H:i:s'))->toBe($this->user->email_verified_at->format('Y-m-d H:i:s'));
})->group('OneToOneTest', 'EloquentRelationship', 'FeatureTest');
