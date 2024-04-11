<?php

use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Role;

beforeEach(function () {
    migrateTables('roles');

    $this->role1 = Role::create(['name' => 'Role 1']);
    $this->role2 = Role::create(['name' => 'Role 2']);
    $this->role3 = Role::create(['name' => 'Role 3']);
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can delete a single record', function () {
    $this->role2->delete();

    expect(Role::count())->toBe(2)
        ->and(Role::withTrashed()->count())->toBe(3)
        ->and(Role::find($this->role2->getKey()))->toBeNull();
})->group('EloquentSoftDeleteTest', 'FeatureTest');

test('deleted record can be retrieved using soft deletes specific feature', function () {
    $this->role2->delete();

    $role = Role::withTrashed()->find($this->role2->getKey());

    expect($role)->not->toBeNull()
        ->and($role->getKey())->toBe($this->role2->getKey())
        ->and($role->name)->toBe($this->role2->name);
})->group('EloquentSoftDeleteTest', 'FeatureTest');

test('it can restore a soft deleted record', function () {
    $this->role2->delete();

    expect(Role::count())->toBe(2)
        ->and(Role::withTrashed()->find($this->role2->getKey()))->not->toBeNull();

    $role = Role::withTrashed()->find($this->role2->getKey());
    $role->restore();

    $role = Role::find($this->role2->getKey());

    expect(Role::count())->toBe(3)
        ->and(Role::whereNotNull('deleted_at')->count())->toBe(0)
        ->and(Role::find($this->role2->getKey()))->not->toBeNull();
})->group('EloquentSoftDeleteTest', 'FeatureTest');
