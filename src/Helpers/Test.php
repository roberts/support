<?php

use Illuminate\Database\Eloquent\Model;

if (! function_exists('randomOrCreate')) {
    /**
     * Get random model or create model using factory.
     */
    function randomOrCreate(string|Model $classNameOrModel): Model
    {
        $className = is_string($classNameOrModel)
            ? $classNameOrModel
            : $classNameOrModel::class;

        if (! class_exists($className)) {
            throw new \InvalidArgumentException("Class {$className} does not exist");
        }

        if (! is_subclass_of($className, Model::class)) {
            throw new \InvalidArgumentException("Class {$className} must extend ".Model::class);
        }

        $existing = $className::inRandomOrder()->first();
        if ($existing instanceof Model) {
            return $existing;
        }

        if (! method_exists($className, 'factory')) {
            throw new \InvalidArgumentException("Model {$className} does not have a factory() method");
        }

        return $className::factory()->create();
    }
}
