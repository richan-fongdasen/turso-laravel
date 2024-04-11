<?php

use Illuminate\Database\Eloquent\Collection;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Post;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

beforeEach(function () {
    migrateTables('users', 'posts');

    $this->user = User::factory()->create();
    $this->post1 = Post::factory()->create([
        'user_id' => $this->user->getKey(),
    ]);
    $this->post2 = Post::factory()->create([
        'user_id' => $this->user->getKey(),
    ]);
});

afterEach(function () {
    DB::getSchemaBuilder()->dropAllTables();
});

test('it can retrieve the related model in one to many relationship', function () {
    $user = User::findOrFail($this->user->getKey());
    $posts = $user->posts;

    expect($posts)->not->toBeEmpty()
        ->and($posts)->toBeInstanceOf(Collection::class)
        ->and($posts->count())->toBe(2)
        ->and($posts->first()->getKey())->toBe($this->post1->getKey())
        ->and($posts->last()->getKey())->toBe($this->post2->getKey())
        ->and($posts->first()->user->getKey())->toBe($this->user->getKey())
        ->and($posts->last()->user->getKey())->toBe($this->user->getKey());
})->group('OneToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in one to many relationship using eager loading', function () {
    $user = User::with('posts')->findOrFail($this->user->getKey());
    $posts = $user->posts;

    expect($posts)->not->toBeEmpty()
        ->and($posts)->toBeInstanceOf(Collection::class)
        ->and($posts->count())->toBe(2)
        ->and($posts->first()->getKey())->toBe($this->post1->getKey())
        ->and($posts->last()->getKey())->toBe($this->post2->getKey())
        ->and($posts->first()->user->getKey())->toBe($this->user->getKey())
        ->and($posts->last()->user->getKey())->toBe($this->user->getKey());
})->group('OneToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in inverted way of one to many relationship', function () {
    $post = Post::findOrFail($this->post1->getKey());
    $user = $post->user;

    expect($user)->not->toBeNull()
        ->and($user->getKey())->toBe($this->user->getKey())
        ->and($user->name)->toBe($this->user->name)
        ->and($user->email)->toBe($this->user->email)
        ->and($user->email_verified_at->format('Y-m-d H:i:s'))->toBe($this->user->email_verified_at->format('Y-m-d H:i:s'));
})->group('OneToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can retrieve the related model in inverted way of one to many relationship using eager loading', function () {
    $post = Post::with('user')->findOrFail($this->post1->getKey());
    $user = $post->user;

    expect($user)->not->toBeNull()
        ->and($user->getKey())->toBe($this->user->getKey())
        ->and($user->name)->toBe($this->user->name)
        ->and($user->email)->toBe($this->user->email)
        ->and($user->email_verified_at->format('Y-m-d H:i:s'))->toBe($this->user->email_verified_at->format('Y-m-d H:i:s'));
})->group('OneToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can create a new Post record using eloquent relationship', function () {
    $user = User::findOrFail($this->user->getKey());
    $post = $user->posts()->create([
        'title'   => 'New Post Title',
        'content' => 'New Post Content',
    ]);

    expect($post)->not->toBeNull()
        ->and($post->getKey())->not->toBeNull()
        ->and($post->user->getKey())->toBe($user->getKey())
        ->and($post->title)->toBe('New Post Title')
        ->and($post->content)->toBe('New Post Content');
})->group('OneToManyTest', 'EloquentRelationship', 'FeatureTest');

test('it can filter the has many relationship by specifying column value', function () {
    $user = User::findOrFail($this->user->getKey());
    $post = $user->posts()->where('title', $this->post1->title)->first();

    expect($post)->not->toBeNull()
        ->and($post->getKey())->toBe($this->post1->getKey())
        ->and($post->title)->toBe($this->post1->title)
        ->and($post->content)->toBe($this->post1->content);
})->group('OneToManyTest', 'EloquentRelationship', 'FeatureTest');
