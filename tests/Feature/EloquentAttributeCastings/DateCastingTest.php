<?php

use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('date_casting_table', function ($table) {
        $table->id();
        $table->date('birthdate');
    });
});

afterEach(function () {
    Schema::dropIfExists('date_casting_table');
});

class DateCastingModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'date_casting_table';

    protected $guarded = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }
}

test('it can insert a new record using Eloquent ORM', function () {
    $birthdate = '1990-01-01';

    DateCastingModel::create([
        'birthdate' => $birthdate,
    ]);

    $result = DateCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->birthdate))->toBe('Illuminate\Support\Carbon')
        ->and($result->birthdate->format('Y-m-d'))->toBe($birthdate);
})->group('DateCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM', function () {
    $birthdate = '1990-01-01';

    DateCastingModel::create([
        'birthdate' => $birthdate,
    ]);

    $newBirthdate = '1995-01-01';

    DateCastingModel::first()->update([
        'birthdate' => $newBirthdate,
    ]);

    $result = DateCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->birthdate))->toBe('Illuminate\Support\Carbon')
        ->and($result->birthdate->format('Y-m-d'))->toBe($newBirthdate);
});

test('it can insert a new record using Eloquent ORM with Carbon instance', function () {
    $birthdate = '1990-01-01';

    DateCastingModel::create([
        'birthdate' => new \Illuminate\Support\Carbon($birthdate),
    ]);

    $result = DateCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->birthdate))->toBe('Illuminate\Support\Carbon')
        ->and($result->birthdate->format('Y-m-d'))->toBe($birthdate);
})->group('DateCastingTest', 'EloquentAttributeCastings', 'FeatureTest');

test('it can update an existing record using Eloquent ORM with Carbon instance', function () {
    $birthdate = '1990-01-01';

    DateCastingModel::create([
        'birthdate' => $birthdate,
    ]);

    $newBirthdate = '1995-01-01';

    DateCastingModel::first()->update([
        'birthdate' => new \Illuminate\Support\Carbon($newBirthdate),
    ]);

    $result = DateCastingModel::first();

    expect($result->id)->toBe(1)
        ->and(get_class($result->birthdate))->toBe('Illuminate\Support\Carbon')
        ->and($result->birthdate->format('Y-m-d'))->toBe($newBirthdate);
});

test('it can find the saved record using Eloquent ORM', function () {
    $birthdate = '1990-01-01';

    DateCastingModel::create([
        'birthdate' => '1995-01-01',
    ]);
    DateCastingModel::create([
        'birthdate' => $birthdate,
    ]);

    $found = DateCastingModel::whereRaw('date("birthdate") = date(?)', [$birthdate])->first();

    expect($found->id)->toBe(2)
        ->and(get_class($found->birthdate))->toBe('Illuminate\Support\Carbon')
        ->and($found->birthdate->format('Y-m-d'))->toBe($birthdate);
})->group('DateCastingTest', 'EloquentAttributeCastings', 'FeatureTest');
