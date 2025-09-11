<?php

namespace Roberts\Support\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Roberts\Support\Tests\Fixtures\Post;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
