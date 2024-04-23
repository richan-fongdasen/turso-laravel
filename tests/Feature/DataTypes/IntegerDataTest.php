<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('integer_table', function ($table) {
        $table->id();
        $table->tinyInteger('tiny_integer');
        $table->smallInteger('small_integer');
        $table->mediumInteger('medium_integer');
        $table->integer('integer');
        $table->bigInteger('big_integer');

        $table->unsignedTinyInteger('unsigned_tiny_integer');
        $table->unsignedSmallInteger('unsigned_small_integer');
        $table->unsignedMediumInteger('unsigned_medium_integer');
        $table->unsignedInteger('unsigned_integer');
        $table->unsignedBigInteger('unsigned_big_integer');
    });
});

afterEach(function () {
    Schema::dropIfExists('integer_table');
});

test('it can insert a new integer data', function () {
    $tinyInteger = 127;
    $smallInteger = 32767;
    $mediumInteger = 8388607;
    $integer = 2147483647;
    $bigInteger = 9223372036854775807;

    $unsignedTinyInteger = 255;
    $unsignedSmallInteger = 65535;
    $unsignedMediumInteger = 16777215;
    $unsignedInteger = 4294967295;
    $unsignedBigInteger = 18446744073709551615;

    $result = DB::table('integer_table')->insert([
        'tiny_integer'   => $tinyInteger,
        'small_integer'  => $smallInteger,
        'medium_integer' => $mediumInteger,
        'integer'        => $integer,
        'big_integer'    => $bigInteger,

        'unsigned_tiny_integer'   => $unsignedTinyInteger,
        'unsigned_small_integer'  => $unsignedSmallInteger,
        'unsigned_medium_integer' => $unsignedMediumInteger,
        'unsigned_integer'        => $unsignedInteger,
        'unsigned_big_integer'    => $unsignedBigInteger,
    ]);

    $newData = DB::table('integer_table')->first();

    expect($result)->toBeTrue()
        ->and(DB::table('integer_table')->count())->toBe(1)
        ->and($newData->tiny_integer)->toBe($tinyInteger)
        ->and($newData->small_integer)->toBe($smallInteger)
        ->and($newData->medium_integer)->toBe($mediumInteger)
        ->and($newData->integer)->toBe($integer)
        ->and($newData->big_integer)->toBe($bigInteger)

        ->and($newData->unsigned_tiny_integer)->toBe($unsignedTinyInteger)
        ->and($newData->unsigned_small_integer)->toBe($unsignedSmallInteger)
        ->and($newData->unsigned_medium_integer)->toBe($unsignedMediumInteger)
        ->and($newData->unsigned_integer)->toBe($unsignedInteger)
        ->and($newData->unsigned_big_integer)->toBe($unsignedBigInteger);
})->group('IntegerDataTest', 'DataTypes', 'FeatureTest');

test('it can update an existing integer data', function () {
    $tinyInteger = 127;
    $smallInteger = 32767;
    $mediumInteger = 8388607;
    $integer = 2147483647;
    $bigInteger = 9223372036854775807;

    $unsignedTinyInteger = 255;
    $unsignedSmallInteger = 65535;
    $unsignedMediumInteger = 16777215;
    $unsignedInteger = 4294967295;
    $unsignedBigInteger = 18446744073709551615;

    DB::table('integer_table')->insert([
        'tiny_integer'   => $tinyInteger,
        'small_integer'  => $smallInteger,
        'medium_integer' => $mediumInteger,
        'integer'        => $integer,
        'big_integer'    => $bigInteger,

        'unsigned_tiny_integer'   => $unsignedTinyInteger,
        'unsigned_small_integer'  => $unsignedSmallInteger,
        'unsigned_medium_integer' => $unsignedMediumInteger,
        'unsigned_integer'        => $unsignedInteger,
        'unsigned_big_integer'    => $unsignedBigInteger,
    ]);

    $newTinyInteger = 63;
    $newSmallInteger = 16383;
    $newMediumInteger = 4194303;
    $newInteger = 1073741823;
    $newBigInteger = 4611686018427387903;

    $newUnsignedTinyInteger = 127;
    $newUnsignedSmallInteger = 32767;
    $newUnsignedMediumInteger = 8388607;
    $newUnsignedInteger = 2147483647;
    $newUnsignedBigInteger = 9223372036854775807;

    $result = DB::table('integer_table')->update([
        'tiny_integer'   => $newTinyInteger,
        'small_integer'  => $newSmallInteger,
        'medium_integer' => $newMediumInteger,
        'integer'        => $newInteger,
        'big_integer'    => $newBigInteger,

        'unsigned_tiny_integer'   => $newUnsignedTinyInteger,
        'unsigned_small_integer'  => $newUnsignedSmallInteger,
        'unsigned_medium_integer' => $newUnsignedMediumInteger,
        'unsigned_integer'        => $newUnsignedInteger,
        'unsigned_big_integer'    => $newUnsignedBigInteger,
    ]);

    $updatedData = DB::table('integer_table')->first();

    expect($result)->toBe(1)
        ->and($updatedData->tiny_integer)->toBe($newTinyInteger)
        ->and($updatedData->small_integer)->toBe($newSmallInteger)
        ->and($updatedData->medium_integer)->toBe($newMediumInteger)
        ->and($updatedData->integer)->toBe($newInteger)
        ->and($updatedData->big_integer)->toBe($newBigInteger)

        ->and($updatedData->unsigned_tiny_integer)->toBe($newUnsignedTinyInteger)
        ->and($updatedData->unsigned_small_integer)->toBe($newUnsignedSmallInteger)
        ->and($updatedData->unsigned_medium_integer)->toBe($newUnsignedMediumInteger)
        ->and($updatedData->unsigned_integer)->toBe($newUnsignedInteger)
        ->and($updatedData->unsigned_big_integer)->toBe($newUnsignedBigInteger);
})->group('IntegerDataTest', 'DataTypes', 'FeatureTest');

test('it can find the saved record', function () {
    $tinyInteger = 127;
    $smallInteger = 32767;
    $mediumInteger = 8388607;
    $integer = 2147483647;
    $bigInteger = 9223372036854775807;

    $unsignedTinyInteger = 255;
    $unsignedSmallInteger = 65535;
    $unsignedMediumInteger = 16777215;
    $unsignedInteger = 4294967295;
    $unsignedBigInteger = 18446744073709551615;

    DB::table('integer_table')->insert([
        'tiny_integer'   => 63,
        'small_integer'  => 16383,
        'medium_integer' => 4194303,
        'integer'        => 1073741823,
        'big_integer'    => 4611686018427387903,

        'unsigned_tiny_integer'   => 127,
        'unsigned_small_integer'  => 32767,
        'unsigned_medium_integer' => 8388607,
        'unsigned_integer'        => 2147483647,
        'unsigned_big_integer'    => 9223372036854775807,
    ]);
    DB::table('integer_table')->insert([
        'tiny_integer'   => $tinyInteger,
        'small_integer'  => $smallInteger,
        'medium_integer' => $mediumInteger,
        'integer'        => $integer,
        'big_integer'    => $bigInteger,

        'unsigned_tiny_integer'   => $unsignedTinyInteger,
        'unsigned_small_integer'  => $unsignedSmallInteger,
        'unsigned_medium_integer' => $unsignedMediumInteger,
        'unsigned_integer'        => $unsignedInteger,
        'unsigned_big_integer'    => $unsignedBigInteger,
    ]);

    $found = DB::table('integer_table')->where('tiny_integer', $tinyInteger)->first();

    expect($found->id)->toBe(2)
        ->and($found->tiny_integer)->toBe($tinyInteger)
        ->and($found->small_integer)->toBe($smallInteger)
        ->and($found->medium_integer)->toBe($mediumInteger)
        ->and($found->integer)->toBe($integer)
        ->and($found->big_integer)->toBe($bigInteger)

        ->and($found->unsigned_tiny_integer)->toBe($unsignedTinyInteger)
        ->and($found->unsigned_small_integer)->toBe($unsignedSmallInteger)
        ->and($found->unsigned_medium_integer)->toBe($unsignedMediumInteger)
        ->and($found->unsigned_integer)->toBe($unsignedInteger)
        ->and($found->unsigned_big_integer)->toBe($unsignedBigInteger);
})->group('IntegerDataTest', 'DataTypes', 'FeatureTest');
