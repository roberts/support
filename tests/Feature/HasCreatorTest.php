<?php

use Illuminate\Support\Facades\Auth;
use Roberts\Support\Tests\Fixtures\Post;
use Roberts\Support\Tests\Fixtures\User;

it('sets creator_id on creating when authenticated', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $post = Post::factory()->create();

    expect($post->creator_id)->toBe($user->id);
    expect($post->creator->is($user))->toBeTrue();
});

it('does not set creator_id when unauthenticated', function () {
    $post = Post::factory()->create();
    expect($post->creator_id)->toBeNull();
});
