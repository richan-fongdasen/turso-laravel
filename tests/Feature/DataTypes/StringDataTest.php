<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('string_table', function ($table) {
        $table->id();
        $table->string('name', length: 100);
    });
});

afterEach(function () {
    Schema::dropIfExists('string_table');
});

test('it can insert a new string data', function () {
    $name = 'John Doe';

    $result = DB::table('string_table')->insert([
        'name' => $name,
    ]);

    $newData = DB::table('string_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('string_table')->count())->toBe(1)
        ->and($newData->name)->toBe($name);
})->group('StringDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing string data', function () {
    $name = 'John Doe';

    DB::table('string_table')->insert([
        'name' => $name,
    ]);

    $newName = 'Jane Doe';

    $result = DB::table('string_table')->update([
        'name' => $newName,
    ]);

    $updatedData = DB::table('string_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->name)->toBe($newName);
})->group('StringDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $name = 'John Doe';

    DB::table('string_table')->insert([
        'name' => 'Jane Doe',
    ]);
    DB::table('string_table')->insert([
        'name' => $name,
    ]);

    $found = DB::table('string_table')->where('name', $name)->first();

    expect($found->id)->toBe(2)
        ->and($found->name)->toBe($name);
})->group('StringDataTest', 'DataTypes', 'FeatureTest');
