<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('timestamp_table', function ($table) {
        $table->id();
        $table->timestamp('added_at', precision: 0);
    });
});

afterEach(function () {
    Schema::dropIfExists('timestamp_table');
});

test('it can insert a new timestamp data', function () {
    $timestamp = '2021-01-01 12:34:56';

    $result = DB::table('timestamp_table')->insert([
        'added_at' => $timestamp,
    ]);

    $newData = DB::table('timestamp_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('timestamp_table')->count())->toBe(1)
        ->and($newData->added_at)->toBe($timestamp);
})->group('TimestampDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing timestamp data', function () {
    $timestamp = '2021-01-01 12:34:56';

    DB::table('timestamp_table')->insert([
        'added_at' => $timestamp,
    ]);

    $newTimestamp = '2021-02-01 23:45:01';

    $result = DB::table('timestamp_table')->update([
        'added_at' => $newTimestamp,
    ]);

    $updatedData = DB::table('timestamp_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->added_at)->toBe($newTimestamp);
})->group('TimestampDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $timestamp = '2021-01-01 12:34:56';

    DB::table('timestamp_table')->insert([
        'added_at' => '2021-02-01 23:45:01',
    ]);
    DB::table('timestamp_table')->insert([
        'added_at' => $timestamp,
    ]);

    $found = DB::table('timestamp_table')->where('added_at', $timestamp)->first();

    expect($found->added_at)->toBe($timestamp);
})->group('TimestampDataTest', 'DataTypes', 'FeatureTest');
