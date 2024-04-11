<?php

use Illuminate\Database\Eloquent\Collection;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Deployment;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Environment;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Project;

beforeEach(function () {
    migrateTables('projects', 'environments', 'deployments');

    $this->project = Project::factory()->create();
    $this->environment = Environment::factory()->create([
        'project_id' => $this->project->getKey(),
    ]);

    $this->deployment1 = Deployment::factory()->create([
        'environment_id' => $this->environment->getKey(),
    ]);
    $this->deployment2 = Deployment::factory()->create([
        'environment_id' => $this->environment->getKey(),
    ]);
    $this->deployment3 = Deployment::factory()->create([
        'environment_id' => $this->environment->getKey(),
    ]);
});

afterEach(function () {
    DB::getSchemaBuilder()->dropAllTables();
});

test('it can retrieve the related model in has many through relationship', function () {
    $project = Project::findOrFail($this->project->getKey());
    $deployments = $project->deployments;

    expect($deployments)->not->toBeEmpty()
        ->and($deployments)->toBeInstanceOf(Collection::class)
        ->and($deployments->count())->toBe(3)
        ->and($deployments->first()->getKey())->toBe($this->deployment1->getKey())
        ->and($deployments->last()->getKey())->toBe($this->deployment3->getKey())
        ->and($deployments->first()->environment->getKey())->toBe($this->environment->getKey())
        ->and($deployments->last()->environment->getKey())->toBe($this->environment->getKey())
        ->and($deployments->first()->environment->project->getKey())->toBe($this->project->getKey())
        ->and($deployments->last()->environment->project->getKey())->toBe($this->project->getKey());
})->group('HasManyThroughTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in has many through relationship using eager loading', function () {
    $project = Project::with('deployments')->findOrFail($this->project->getKey());
    $deployments = $project->deployments;

    expect($deployments)->not->toBeEmpty()
        ->and($deployments)->toBeInstanceOf(Collection::class)
        ->and($deployments->count())->toBe(3)
        ->and($deployments->first()->getKey())->toBe($this->deployment1->getKey())
        ->and($deployments->last()->getKey())->toBe($this->deployment3->getKey())
        ->and($deployments->first()->environment->getKey())->toBe($this->environment->getKey())
        ->and($deployments->last()->environment->getKey())->toBe($this->environment->getKey())
        ->and($deployments->first()->environment->project->getKey())->toBe($this->project->getKey())
        ->and($deployments->last()->environment->project->getKey())->toBe($this->project->getKey());
})->group('HasManyThroughTest', 'EloquentRelationship', 'FeatureTest');
