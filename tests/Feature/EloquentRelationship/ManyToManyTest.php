<?php

use Illuminate\Database\Eloquent\Collection;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Role;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

beforeEach(function () {
    migrateTables('users', 'roles', 'user_roles');

    $this->user = User::factory()->create();
    $this->role1 = Role::factory()->create();
    $this->role2 = Role::factory()->create();
    $this->role3 = Role::factory()->create();

    $this->user->roles()->attach($this->role1->getKey());
    $this->user->roles()->attach($this->role3->getKey());
});

afterEach(function () {
    DB::getSchemaBuilder()->dropAllTables();
});

test('it can retrieve the related model in many to many relationship', function () {
    $user = User::findOrFail($this->user->getKey());
    $roles = $user->roles;

    expect($roles)->not->toBeEmpty()
        ->and($roles->count())->toBe(2)
        ->and($roles->first()->getKey())->toBe($this->role1->getKey())
        ->and($roles->last()->getKey())->toBe($this->role3->getKey());
})->group('ManyToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in many to many relationship using eager loading', function () {
    $user = User::with('roles')->findOrFail($this->user->getKey());
    $roles = $user->roles;

    expect($roles)->not->toBeEmpty()
        ->and($roles->count())->toBe(2)
        ->and($roles->first()->getKey())->toBe($this->role1->getKey())
        ->and($roles->last()->getKey())->toBe($this->role3->getKey());
})->group('ManyToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in inverted way of many to many relationship', function () {
    $role = Role::findOrFail($this->role1->getKey());
    $users = $role->users;

    expect($users)->not->toBeEmpty()
        ->and($users)->toBeInstanceOf(Collection::class)
        ->and($users->count())->toBe(1)
        ->and($users->first()->getKey())->toBe($this->user->getKey());
})->group('ManyToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in inverted way of many to many relationship using eager loading', function () {
    $role = Role::with('users')->findOrFail($this->role1->getKey());
    $users = $role->users;

    expect($users)->not->toBeEmpty()
        ->and($users)->toBeInstanceOf(Collection::class)
        ->and($users->count())->toBe(1)
        ->and($users->first()->getKey())->toBe($this->user->getKey());
})->group('ManyToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can filter the many to many relationship by specifying a column value', function () {
    $user = User::findOrFail($this->user->getKey());
    $role = $user->roles()->where('name', $this->role3->name)->first();

    expect($role)->not->toBeNull()
        ->and($role->getKey())->toBe($this->role3->getKey())
        ->and($role->name)->toBe($this->role3->name);
})->group('ManyToManyTest', 'EloquentRelationship', 'FeatureTest');
