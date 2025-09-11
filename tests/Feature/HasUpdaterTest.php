<?php

use Illuminate\Support\Facades\Auth;
use Roberts\Support\Tests\Fixtures\Post;
use Roberts\Support\Tests\Fixtures\User;

it('sets updater_id on save when authenticated', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $post = Post::factory()->create();
    expect($post->updater_id)->toBe($user->id);
    expect($post->updater->is($user))->toBeTrue();
});

it('updates updater_id on subsequent saves with different user', function () {
    $first = User::factory()->create();
    $second = User::factory()->create();

    Auth::login($first);
    $post = Post::factory()->create();

    Auth::logout();
    Auth::login($second);
    $post->update(['title' => 'Updated']);

    expect($post->updater_id)->toBe($second->id);
});
