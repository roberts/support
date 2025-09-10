<?php

namespace Roberts\Support;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Roberts\Support\Commands\SupportCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_support_table')
            ->hasCommand(SupportCommand::class);
    }
}
