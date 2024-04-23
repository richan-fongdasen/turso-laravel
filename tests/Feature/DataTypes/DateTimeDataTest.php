<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('datetime_table', function ($table) {
        $table->id();
        $table->dateTime('published_at');
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('datetime_table');
});

test('it can insert a new datetime data', function () {
    $publishedAt = now();

    $result = DB::table('datetime_table')->insert([
        'published_at' => $publishedAt,
    ]);

    $newData = DB::table('datetime_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('datetime_table')->count())->toBe(1)
        ->and($newData->published_at)->toBe($publishedAt->format('Y-m-d H:i:s'));
})->group('DateTimeDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing datetime data', function () {
    $publishedAt = now();

    DB::table('datetime_table')->insert([
        'published_at' => $publishedAt,
    ]);

    $newPublishedAt = now()->subDay();

    $result = DB::table('datetime_table')->update([
        'published_at' => $newPublishedAt,
    ]);

    $updatedData = DB::table('datetime_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->published_at)->toBe($newPublishedAt->format('Y-m-d H:i:s'));
})->group('DateTimeDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $publishedAt = now();

    DB::table('datetime_table')->insert([
        'published_at' => now()->subDay(),
    ]);
    DB::table('datetime_table')->insert([
        'published_at' => $publishedAt,
    ]);

    $found = DB::table('datetime_table')->where('published_at', '>=', $publishedAt->format('Y-m-d H:i:s'))->first();

    expect($found->id)->toBe(2)
        ->and($found->published_at)->toBe($publishedAt->format('Y-m-d H:i:s'));
})->group('DateTimeDataTest', 'DataTypes', 'FeatureTest');
