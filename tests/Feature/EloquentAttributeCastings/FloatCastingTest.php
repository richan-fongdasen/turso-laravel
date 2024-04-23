<?php

use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('float_casting_table', function ($table) {
        $table->id();
        $table->float('amount');
    });
});

afterEach(function () {
    Schema::dropIfExists('float_casting_table');
});

class FloatCastingModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'float_casting_table';

    protected $guarded = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }
}

test('it can insert a new record using Eloquent ORM', function () {
    $amount = 100.50;

    FloatCastingModel::create([
        'amount' => $amount,
    ]);

    $result = FloatCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->amount))->toBe('double')
        ->and($result->amount)->toBe($amount);
})->group('FloatCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM', function () {
    $amount = 100.50;

    FloatCastingModel::create([
        'amount' => $amount,
    ]);

    $newAmount = 200.75;

    FloatCastingModel::first()->update([
        'amount' => $newAmount,
    ]);

    $result = FloatCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->amount))->toBe('double')
        ->and($result->amount)->toBe($newAmount);
})->group('FloatCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can find the saved record using Eloquent ORM', function () {
    $amount = 100.50;

    FloatCastingModel::create([
        'amount' => $amount,
    ]);

    $result = FloatCastingModel::where('amount', $amount)->first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->amount))->toBe('double')
        ->and($result->amount)->toBe($amount);
})->group('FloatCastingTest', 'EloquentAttributeCastings', 'FeatureTest');
