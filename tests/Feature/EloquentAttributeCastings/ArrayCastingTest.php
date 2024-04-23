<?php

use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('array_casting_table', function ($table) {
        $table->id();
        $table->json('data');
    });
});

afterEach(function () {
    Schema::dropIfExists('array_casting_table');
});

class ArrayCastingModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'array_casting_table';

    protected $guarded = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}

test('it can insert a new record using Eloquent ORM', function () {
    $data = ['name' => 'John Doe', 'city' => 'New York'];

    ArrayCastingModel::create([
        'data' => $data,
    ]);

    $result = ArrayCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->data))->toBe('array')
        ->and($result->data)->toBe($data);
})->group('ArrayCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM', function () {
    $data = ['name' => 'John Doe', 'city' => 'New York'];

    ArrayCastingModel::create([
        'data' => $data,
    ]);

    $newData = ['name' => 'Jane Doe', 'city' => 'Los Angeles'];

    ArrayCastingModel::first()->update([
        'data' => $newData,
    ]);

    $result = ArrayCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(gettype($result->data))->toBe('array')
        ->and($result->data)->toBe($newData);
})->group('ArrayCastingTest', 'EloquentAttributeCastings', 'FeatureTest');
