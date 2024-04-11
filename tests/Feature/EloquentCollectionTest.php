<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Project;

beforeEach(function () {
    migrateTables('projects');

    Project::factory()->count(3)->create();
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can perform the fresh() method', function () {
    $collection = Project::all();

    $collection->first()->delete();

    $freshCollection = $collection->fresh();

    expect($freshCollection)->toBeInstanceOf(Collection::class)
        ->and($freshCollection->count())->toBe($collection->count() - 1);
})->group('EloquentCollectionTest', 'FeatureTest');
