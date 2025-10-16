<?php

namespace Roberts\Support;

use Roberts\Support\Commands\InitCommand;
use Roberts\Support\Commands\UpdateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SupportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('support')
            ->hasCommands([
                InitCommand::class,
                UpdateCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        // Load Blade views from stubs directory
        $this->loadViewsFrom(__DIR__.'/../stubs', 'support');
    }
}
