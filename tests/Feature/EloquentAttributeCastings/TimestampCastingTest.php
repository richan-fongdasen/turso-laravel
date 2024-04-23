<?php

use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('timestamp_casting_table', function ($table) {
        $table->id();
        $table->timestamp('added_at');
    });
});

afterEach(function () {
    Schema::dropIfExists('timestamp_casting_table');
});

class TimestampCastingModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'timestamp_casting_table';

    protected $guarded = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'added_at' => 'timestamp',
        ];
    }
}

test('it can insert a new record using Eloquent ORM', function () {
    $addedAt = now();

    TimestampCastingModel::create([
        'added_at' => $addedAt,
    ]);

    $result = TimestampCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->added_at))->toBe('integer')
        ->and($result->added_at)->toBe($addedAt->timestamp);
})->group('TimestampCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM', function () {
    $addedAt = now();

    TimestampCastingModel::create([
        'added_at' => $addedAt,
    ]);

    $newAddedAt = now()->addHour();

    TimestampCastingModel::first()->update([
        'added_at' => $newAddedAt,
    ]);

    $result = TimestampCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->added_at))->toBe('integer')
        ->and($result->added_at)->toBe($newAddedAt->timestamp);
})->group('TimestampCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can retrieve a record using Eloquent ORM', function () {
    $addedAt = now();

    TimestampCastingModel::create([
        'added_at' => $addedAt,
    ]);

    $result = TimestampCastingModel::where('added_at', $addedAt)->first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->added_at))->toBe('integer')
        ->and($result->added_at)->toBe($addedAt->timestamp);
})->group('TimestampCastingTest', 'EloquentAttributeCastings', 'FeatureTest');
