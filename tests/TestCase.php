<?php

namespace Roberts\Support\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Roberts\Support\SupportServiceProvider;
use Roberts\Support\Tests\Fixtures\User;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Roberts\\Support\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SupportServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        // Configure default user provider to our local test User model
        config()->set('auth.providers.users.model', User::class);

        // Create in-memory schema for users and posts
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('posts', function ($table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->timestamps();
        });

        // Individual test tables for status traits
        Schema::create('test_active_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('test_publishing_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('test_processing_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('test_approval_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('test_application_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('test_order_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('test_subscription_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('test_moderator_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });
    }
}
