<?php

namespace Roberts\Support\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Roberts\Support\Tests\Fixtures\User;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];
    }
}
