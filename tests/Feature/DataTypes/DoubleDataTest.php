<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('double_table', function ($table) {
        $table->id();
        $table->double('amount');
    });
});

afterEach(function () {
    Schema::dropIfExists('double_table');
});

test('it can insert a new double data', function () {
    $amount = 123.45;

    $result = DB::table('double_table')->insert([
        'amount' => $amount,
    ]);

    $newData = DB::table('double_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('double_table')->count())->toBe(1)
        ->and($newData->amount)->toBe($amount);
})->group('DoubleDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing double data', function () {
    $amount = 123.45;

    DB::table('double_table')->insert([
        'amount' => $amount,
    ]);

    $newAmount = 543.21;

    $result = DB::table('double_table')->update([
        'amount' => $newAmount,
    ]);

    $updatedData = DB::table('double_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->amount)->toBe($newAmount);
})->group('DoubleDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $amount = 123.45;

    DB::table('double_table')->insert([
        'amount' => 543.21,
    ]);
    DB::table('double_table')->insert([
        'amount' => $amount,
    ]);

    $found = DB::table('double_table')->where('amount', $amount)->first();

    expect($found->id)->toBe(2)
        ->and($found->amount)->toBe($amount);
})->group('DoubleDataTest', 'DataTypes', 'FeatureTest');
