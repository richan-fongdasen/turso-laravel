<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Project;

beforeEach(function () {
    migrateTables('projects');

    $this->project1 = Project::factory()->create();
    $this->project2 = Project::factory()->create();
    $this->project3 = Project::factory()->create();
});

afterEach(function () {
    Schema::dropAllTables();
});

test('it can get all rows from the table', function () {
    $projects = DB::table('projects')->get();

    expect($projects)->toHaveCount(3)
        ->and($projects[0]->name)->toEqual($this->project1->name)
        ->and($projects[1]->name)->toEqual($this->project2->name)
        ->and($projects[2]->name)->toEqual($this->project3->name);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can retrieve a single row from the table with first() method', function () {
    $project = DB::table('projects')->where('name', $this->project2->name)->first();

    expect($project)->not->toBeNull()
        ->and($project)->toBeObject()
        ->and($project->id)->toEqual($this->project2->id)
        ->and($project->name)->toEqual($this->project2->name);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can retrieve a single row from the table with find() method', function () {
    $project = DB::table('projects')->find($this->project2->getKey());

    expect($project)->not->toBeNull()
        ->and($project)->toBeObject()
        ->and($project->id)->toEqual($this->project2->id)
        ->and($project->name)->toEqual($this->project2->name);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it will return null if there was no record with the given id to be found', function () {
    $project = DB::table('projects')->find(999);

    expect($project)->toBeNull();
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can retrieve a list of column values', function () {
    $expectation = [
        $this->project1->name,
        $this->project2->name,
        $this->project3->name,
    ];

    $projects = DB::table('projects')->pluck('name')->toArray();

    expect($projects)->toBeArray()
        ->and($projects)->toHaveCount(3)
        ->and($projects)->toEqual($expectation);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can stream the results lazily', function () {
    $expectations = [
        $this->project1,
        $this->project2,
        $this->project3,
    ];

    DB::table('projects')
        ->orderBy('id')
        ->lazy()
        ->each(function (object $project, int $index) use ($expectations) {
            expect($project)->toBeObject()
                ->and($project->id)->toEqual($expectations[$index]->id)
                ->and($project->name)->toEqual($expectations[$index]->name);
        });
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can count the records count', function () {
    expect(DB::table('projects')->count())->toEqual(3);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can return the maximum value of a column from the table', function () {
    expect(DB::table('projects')->max('id'))->toEqual(3);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can return the minimum value of a column from the table', function () {
    expect(DB::table('projects')->min('id'))->toEqual(1);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can return the average value of a column from the table', function () {
    expect(DB::table('projects')->avg('id'))->toEqual(2);
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');

test('it can determine if a record exists in the table', function () {
    expect(DB::table('projects')->where('name', $this->project2->name)->exists())->toBeTrue()
        ->and(DB::table('projects')->where('name', 'unknown')->doesntExist())->toBeTrue();
})->group('DatabaseQueriesTest', 'QueryBuilder', 'FeatureTest');
