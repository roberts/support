<?php

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Tests\Fixtures\Post;
use Roberts\Support\Tests\Fixtures\User;

it('returns a random existing model when records exist', function () {
    User::factory()->count(3)->create();

    $picked = randomOrCreate(User::class);
    expect($picked)->toBeInstanceOf(Model::class)
        ->and($picked)->toBeInstanceOf(User::class);
});

it('creates a model using factory when none exist', function () {
    expect(User::count())->toBe(0);
    $created = randomOrCreate(User::class);
    expect($created)->toBeInstanceOf(User::class)
        ->and(User::count())->toBe(1);
});

it('accepts a model instance as input', function () {
    $post = Post::factory()->create();
    $picked = randomOrCreate($post);
    expect($picked)->toBeInstanceOf(Post::class);
});

it('throws on invalid input', function () {
    expect(fn () => randomOrCreate(123))->toThrow(InvalidArgumentException::class);
});
