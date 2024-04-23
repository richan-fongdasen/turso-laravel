<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('boolean_table', function ($table) {
        $table->id();
        $table->boolean('confirmed');
    });
});

afterEach(function () {
    Schema::dropIfExists('boolean_table');
});

test('it can insert a new boolean data, and the value will be saved as an integer', function () {
    $result = DB::table('boolean_table')->insert([
        'confirmed' => true,
    ]);

    $newData = DB::table('boolean_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('boolean_table')->count())->toBe(1)
        ->and($newData->confirmed)->toBe(1);
})->group('BooleanDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing boolean data, and the retrieved value will be an integer', function () {
    DB::table('boolean_table')->insert([
        'confirmed' => true,
    ]);

    $result = DB::table('boolean_table')->update([
        'confirmed' => false,
    ]);

    $updatedData = DB::table('boolean_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->confirmed)->toBe(0);
})->group('BooleanDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    DB::table('boolean_table')->insert([
        'confirmed' => true,
    ]);
    DB::table('boolean_table')->insert([
        'confirmed' => false,
    ]);

    $found = DB::table('boolean_table')->where('confirmed', false)->first();

    expect($found->id)->toBe(2)
        ->and($found->confirmed)->toBe(0);
})->group('BooleanDataTest', 'DataTypes', 'FeatureTest');
