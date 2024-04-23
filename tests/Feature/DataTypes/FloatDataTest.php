<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('float_table', function ($table) {
        $table->id();
        $table->float('amount', precision: 53);
    });
});

afterEach(function () {
    Schema::dropIfExists('float_table');
});

test('it can insert a new float data', function () {
    $amount = 123.45;

    $result = DB::table('float_table')->insert([
        'amount' => $amount,
    ]);

    $newData = DB::table('float_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('float_table')->count())->toBe(1)
        ->and($newData->amount)->toBe($amount);
})->group('FloatDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing float data', function () {
    $amount = 123.45;

    DB::table('float_table')->insert([
        'amount' => $amount,
    ]);

    $newAmount = 543.21;

    $result = DB::table('float_table')->update([
        'amount' => $newAmount,
    ]);

    $updatedData = DB::table('float_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->amount)->toBe($newAmount);
})->group('FloatDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $amount = 123.45;

    DB::table('float_table')->insert([
        'amount' => 543.21,
    ]);
    DB::table('float_table')->insert([
        'amount' => $amount,
    ]);

    $found = DB::table('float_table')->where('amount', $amount)->first();

    expect($found->id)->toBe(2)
        ->and($found->amount)->toBe($amount);
})->group('FloatDataTest', 'DataTypes', 'FeatureTest');
