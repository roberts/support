<?php

declare(strict_types=1);

namespace Roberts\Support\Services;

use Illuminate\Console\Command;

class ProjectDetector
{
    public function detectProjectType(Command $command): string
    {
        $composer = $this->getComposerData();

        // Check for package indicators
        if (isset($composer['require-dev']['orchestra/testbench'])) {
            return 'package';
        }

        // Check for app indicators
        if (is_dir(base_path('app')) &&
            isset($composer['require']['laravel/framework'])) {
            return 'app';
        }

        // Fallback to prompt
        return $command->choice(
            'What type of project is this?',
            ['app', 'package'],
            0
        );
    }

    public function isPackage(): bool
    {
        $composer = $this->getComposerData();

        return isset($composer['require-dev']['orchestra/testbench']) ||
               ! is_dir(base_path('app'));
    }

    public function isApp(): bool
    {
        return ! $this->isPackage();
    }

    protected function getComposerData(): array
    {
        $composerPath = base_path('composer.json');

        if (! file_exists($composerPath)) {
            return [];
        }

        return json_decode(file_get_contents($composerPath), true) ?? [];
    }
}
