<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('year_table', function ($table) {
        $table->id();
        $table->year('birth_year');
    });
});

afterEach(function () {
    Schema::dropIfExists('year_table');
});

test('it can insert a new year data', function () {
    $year = 2021;

    $result = DB::table('year_table')->insert([
        'birth_year' => $year,
    ]);

    $newData = DB::table('year_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('year_table')->count())->toBe(1)
        ->and($newData->birth_year)->toBe($year);
})->group('YearDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing year data', function () {
    $year = 2021;

    DB::table('year_table')->insert([
        'birth_year' => $year,
    ]);

    $newYear = 2022;

    $result = DB::table('year_table')->update([
        'birth_year' => $newYear,
    ]);

    $updatedData = DB::table('year_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->birth_year)->toBe($newYear);
})->group('YearDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $year = 2021;

    DB::table('year_table')->insert([
        'birth_year' => 2022,
    ]);
    DB::table('year_table')->insert([
        'birth_year' => $year,
    ]);

    $found = DB::table('year_table')->where('birth_year', $year)->first();

    expect($found->birth_year)->toBe($year);
})->group('YearDataTest', 'DataTypes', 'FeatureTest');
