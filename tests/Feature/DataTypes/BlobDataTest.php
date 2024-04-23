<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('blob_table', function ($table) {
        $table->id();
        $table->binary('blob');
    });
});

afterEach(function () {
    Schema::dropIfExists('blob_table');
});

test('it can insert a new blob data', function () {
    $data = random_bytes(50);

    $result = DB::table('blob_table')->insert([
        'blob' => $data,
    ]);

    $newData = DB::table('blob_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('blob_table')->count())->toBe(1)
        ->and($newData->blob)->toBe($data);
})->group('BlobDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing blob data', function () {
    $data = random_bytes(50);

    DB::table('blob_table')->insert([
        'blob' => $data,
    ]);

    $newData = random_bytes(50);

    $result = DB::table('blob_table')->update([
        'blob' => $newData,
    ]);

    $updatedData = DB::table('blob_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->blob)->toBe($newData);
})->group('BlobDataTest', 'DataTypes', 'FeatureTest');
