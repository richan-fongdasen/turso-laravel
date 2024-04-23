<?php

use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('double_casting_table', function ($table) {
        $table->id();
        $table->double('amount');
    });
});

afterEach(function () {
    Schema::dropIfExists('double_casting_table');
});

class DoubleCastingModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'double_casting_table';

    protected $guarded = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'amount' => 'double',
        ];
    }
}

test('it can insert a new record using Eloquent ORM', function () {
    $amount = 100.50;

    DoubleCastingModel::create([
        'amount' => $amount,
    ]);

    $result = DoubleCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->amount))->toBe('double')
        ->and($result->amount)->toBe($amount);
})->group('DoubleCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM', function () {
    $amount = 100.50;

    DoubleCastingModel::create([
        'amount' => $amount,
    ]);

    $newAmount = 200.75;

    DoubleCastingModel::first()->update([
        'amount' => $newAmount,
    ]);

    $result = DoubleCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->amount))->toBe('double')
        ->and($result->amount)->toBe($newAmount);
})->group('DoubleCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can find the saved record using Eloquent ORM', function () {
    $amount = 100.50;

    DoubleCastingModel::create([
        'amount' => $amount,
    ]);

    $result = DoubleCastingModel::where('amount', $amount)->first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->amount))->toBe('double')
        ->and($result->amount)->toBe($amount);
})->group('DoubleCastingTest', 'EloquentAttributeCastings', 'FeatureTest');
