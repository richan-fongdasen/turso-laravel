<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('text_table', function ($table) {
        $table->id();
        $table->string('description');
    });
});

afterEach(function () {
    Schema::dropIfExists('text_table');
});

test('it can insert a new text data', function () {
    $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';

    $result = DB::table('text_table')->insert([
        'description' => $description,
    ]);

    $newData = DB::table('text_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('text_table')->count())->toBe(1)
        ->and($newData->description)->toBe($description);
})->group('TextDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing text data', function () {
    $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';

    DB::table('text_table')->insert([
        'description' => $description,
    ]);

    $newDescription = 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $result = DB::table('text_table')->update([
        'description' => $newDescription,
    ]);

    $updatedData = DB::table('text_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->description)->toBe($newDescription);
})->group('TextDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';

    DB::table('text_table')->insert([
        'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    ]);
    DB::table('text_table')->insert([
        'description' => $description,
    ]);

    $found = DB::table('text_table')->where('description', $description)->first();

    expect($found->id)->toBe(2)
        ->and($found->description)->toBe($description);
})->group('TextDataTest', 'DataTypes', 'FeatureTest');
