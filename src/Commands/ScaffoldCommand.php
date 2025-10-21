<?php

declare(strict_types=1);

namespace Roberts\Support\Commands;

use Illuminate\Console\Command;
use Roberts\Support\Services\FeatureDetector;
use Roberts\Support\Services\ProjectDetector;
use Roberts\Support\Services\StubPublisher;

class ScaffoldCommand extends Command
{
    protected $signature = 'support:scaffold';

    protected $description = 'Scaffold GitHub Actions, Docker, and configuration files for your project';

    protected array $features = [];

    protected ?array $config = null;

    public function handle(
        ProjectDetector $detector,
        FeatureDetector $featureDetector,
        StubPublisher $publisher
    ): int {
        $this->info('🚀 Scaffolding your Laravel project...');
        $this->newLine();

        // Auto-detect project type
        $type = $detector->detectProjectType($this);
        $this->features = $featureDetector->detect();

        $this->info("Detected: Laravel {$type}");
        $this->newLine();

        if ($type === 'app') {
            // Show detected features
            $this->displayFeatures($this->features);

            if (! $this->confirm('Proceed with scaffolding?', true)) {
                $this->warn('Cancelled.');

                return self::FAILURE;
            }

            $publisher->publishAppFiles($this, [], $this->features);
        } else {
            $publisher->publishPackageFiles($this);
        }

        $this->displayNextSteps($type);

        return self::SUCCESS;
    }

    protected function displayFeatures(array $features): void
    {
        if (count(array_filter($features)) > 0) {
            $this->newLine();
            $this->line('<fg=yellow>Detected Features:</>');
            foreach ($features as $feature => $enabled) {
                if ($enabled) {
                    $this->line('  ✓ '.ucfirst(str_replace('has', '', $feature)));
                }
            }
            $this->newLine();
        }
    }

    protected function getComposerName(): string
    {
        $composer = json_decode(
            file_get_contents(base_path('composer.json')),
            true
        );

        return $composer['name'] ?? 'my-project';
    }



    protected function displayNextSteps(string $type): void
    {
        $this->newLine();
        $this->info('✅ Files published successfully!');
        $this->newLine(2);

        if ($type === 'app') {
            $this->displayAppNextSteps($this->features);
        } else {
            $this->displayPackageNextSteps();
        }
    }

    protected function displayAppNextSteps(array $features): void
    {
        $this->line('<fg=cyan>═══════════════════════════════════════════════════════════════</>');
        $this->line('<fg=yellow;options=bold>  📋 APP SETUP COMPLETE</>');
        $this->line('<fg=cyan>═══════════════════════════════════════════════════════════════</>');
        $this->newLine();

        $this->line('✅ GitHub workflows installed:');
        $this->line('   • Tests (runs on push)');
        $this->line('   • PHPStan (static analysis)');
        $this->line('   • Code style (Pint)');
        $this->newLine();

        $this->line('✅ Docker files created:');
        $this->line('   • Dockerfile (multi-stage build)');
        $this->line('   • .dockerignore');
        $this->line('   • docker-entrypoint.sh');
        $this->line('   • .gcloudignore');
        $this->newLine();

        $this->line('✅ Development tools:');
        $this->line('   • PHPStan configuration');
        $this->line('   • VS Code settings & extensions');
        $this->newLine();

        if ($features['hasFlux'] ?? false) {
            $this->line('<fg=yellow>Note:</> Livewire Flux detected - Add GitHub secrets for CI:');
            $this->line('   • <fg=white>FLUX_USERNAME</> = your-flux-username');
            $this->line('   • <fg=white>FLUX_LICENSE_KEY</> = your-flux-key');
            $this->newLine();
        }

        $this->line('🎯 Your Laravel app is ready for development!');
        $this->newLine();
        $this->comment('📚 Documentation: https://github.com/drewroberts/support');
    }

    protected function displayPackageNextSteps(): void
    {
        $this->line('<fg=cyan>═══════════════════════════════════════════════════════════════</>');
        $this->line('<fg=yellow;options=bold>  📋 PACKAGE SETUP COMPLETE</>');
        $this->line('<fg=cyan>═══════════════════════════════════════════════════════════════</>');
        $this->newLine();

        $this->line('✅ GitHub workflows installed:');
        $this->line('   • Tests (runs on push)');
        $this->line('   • PHPStan (static analysis)');
        $this->line('   • Code style (Pint)');
        $this->newLine();

        $this->line('🎯 Your package is ready for development!');
        $this->newLine();
        $this->comment('📚 Documentation: https://github.com/drewroberts/support');
    }
}
