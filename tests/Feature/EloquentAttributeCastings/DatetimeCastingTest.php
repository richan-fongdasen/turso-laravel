<?php

use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('datetime_casting_table', function ($table) {
        $table->id();
        $table->dateTime('started_at');
    });
});

afterEach(function () {
    Schema::dropIfExists('datetime_casting_table');
});

class DatetimeCastingModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'datetime_casting_table';

    protected $guarded = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
        ];
    }
}

test('it can insert a new record using Eloquent ORM', function () {
    $startedAt = '2021-01-01 12:00:00';

    DatetimeCastingModel::create([
        'started_at' => $startedAt,
    ]);

    $result = DatetimeCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->started_at))->toBe('Illuminate\Support\Carbon')
        ->and($result->started_at->format('Y-m-d H:i:s'))->toBe($startedAt);
})->group('DatetimeCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM', function () {
    $startedAt = '2021-01-01 12:00:00';

    DatetimeCastingModel::create([
        'started_at' => $startedAt,
    ]);

    $newStartedAt = '2021-01-01 13:00:00';

    DatetimeCastingModel::first()->update([
        'started_at' => $newStartedAt,
    ]);

    $result = DatetimeCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->started_at))->toBe('Illuminate\Support\Carbon')
        ->and($result->started_at->format('Y-m-d H:i:s'))->toBe($newStartedAt);
})->group('DatetimeCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can insert a new record using Eloquent ORM with Carbon instance', function () {
    $startedAt = now();

    DatetimeCastingModel::create([
        'started_at' => $startedAt,
    ]);

    $result = DatetimeCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->started_at))->toBe('Illuminate\Support\Carbon')
        ->and($result->started_at->format('Y-m-d H:i:s'))->toBe($startedAt->format('Y-m-d H:i:s'));
})->group('DatetimeCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM with Carbon instance', function () {
    $startedAt = now();

    DatetimeCastingModel::create([
        'started_at' => $startedAt,
    ]);

    $newStartedAt = now()->addHour();

    DatetimeCastingModel::first()->update([
        'started_at' => $newStartedAt,
    ]);

    $result = DatetimeCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->started_at))->toBe('Illuminate\Support\Carbon')
        ->and($result->started_at->format('Y-m-d H:i:s'))->toBe($newStartedAt->format('Y-m-d H:i:s'));
})->group('DatetimeCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can find the saved record using Eloquent ORM', function () {
    $startedAt = now();

    DatetimeCastingModel::create([
        'started_at' => now()->subHour(),
    ]);
    DatetimeCastingModel::create([
        'started_at' => $startedAt,
    ]);

    $found = DatetimeCastingModel::where('started_at', $startedAt)->first();

    expect($found->id)->toBe(2)
        ->and(get_class($found->started_at))->toBe('Illuminate\Support\Carbon')
        ->and($found->started_at->format('Y-m-d H:i:s'))->toBe($startedAt->format('Y-m-d H:i:s'));
})->group('DatetimeCastingTest', 'EloquentAttributeCastings', 'FeatureTest');
