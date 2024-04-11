<?php

use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Project;

beforeEach(function () {
    migrateTables('projects');

    $this->project1 = Project::create(['name' => 'Project 1']);
    $this->project2 = Project::create(['name' => 'Project 2']);
    $this->project3 = Project::create(['name' => 'Project 3']);
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can delete a single record', function () {
    $this->project2->delete();

    expect(Project::count())->toBe(2)
        ->and(Project::find($this->project2->getKey()))->toBeNull();
})->group('EloquentDeleteTest', 'FeatureTest');

test('it can delete multiple records using query', function () {
    Project::whereIn('id', [$this->project1->getKey(), $this->project3->getKey()])->delete();

    expect(Project::count())->toBe(1)
        ->and(Project::find($this->project1->getKey()))->toBeNull()
        ->and(Project::find($this->project3->getKey()))->toBeNull();
})->group('EloquentDeleteTest', 'FeatureTest');

test('it can delete multiple records using destroy method', function () {
    Project::destroy([$this->project1->getKey(), $this->project3->getKey()]);

    expect(Project::count())->toBe(1)
        ->and(Project::find($this->project1->getKey()))->toBeNull()
        ->and(Project::find($this->project3->getKey()))->toBeNull();
})->group('EloquentDeleteTest', 'FeatureTest');

test('it can truncate the whole table', function () {
    Project::truncate();

    expect(Project::count())->toBe(0)
        ->and(Project::find($this->project1->getKey()))->toBeNull()
        ->and(Project::find($this->project2->getKey()))->toBeNull()
        ->and(Project::find($this->project3->getKey()))->toBeNull();
})->group('EloquentDeleteTest', 'FeatureTest');
