<?php

declare(strict_types=1);

namespace Roberts\Support\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class StubPublisher
{
    public function publishAppFiles(Command $command, array $config, array $features, bool $updateMode = false): void
    {
        $command->info('Publishing app files...');
        $command->newLine();

        $files = [
            // GitHub Workflows
            ['support::stubs.app.github.workflows.tests', '.github/workflows/tests.yml'],
            ['support::stubs.app.github.workflows.phpstan', '.github/workflows/phpstan.yml'],
            ['support::stubs.app.github.workflows.lint', '.github/workflows/lint.yml'],

            // Docker files
            ['support::stubs.app.docker.Dockerfile', 'Dockerfile'],
            ['support::stubs.app.docker.dockerignore', '.dockerignore'],
            ['support::stubs.app.docker.entrypoint', 'docker-entrypoint.sh'],
            ['support::stubs.app.docker.gcloudignore', '.gcloudignore'],

            // PHPStan
            ['support::stubs.app.phpstan', 'phpstan.neon.dist'],

            // VS Code
            ['support::stubs.app.vscode.settings', '.vscode/settings.json'],
            ['support::stubs.app.vscode.extensions', '.vscode/extensions.json'],
        ];

        $data = array_merge($config, $features);

        foreach ($files as [$view, $destination]) {
            $this->publishFile($command, $view, base_path($destination), $data, $updateMode);
        }

        // Make entrypoint executable
        if (file_exists(base_path('docker-entrypoint.sh'))) {
            chmod(base_path('docker-entrypoint.sh'), 0755);
        }
    }

    public function publishPackageFiles(Command $command, bool $updateMode = false): void
    {
        $command->info('Publishing package files...');
        $command->newLine();

        $files = [
            // GitHub Workflows
            ['support::stubs.package.github.workflows.tests', '.github/workflows/run-tests.yml'],
            ['support::stubs.package.github.workflows.phpstan', '.github/workflows/phpstan.yml'],
            ['support::stubs.package.github.workflows.lint', '.github/workflows/fix-php-code-style-issues.yml'],

            // PHPStan
            ['support::stubs.package.phpstan', 'phpstan.neon.dist'],
        ];

        foreach ($files as [$view, $destination]) {
            $this->publishFile($command, $view, base_path($destination), [], $updateMode);
        }
    }

    protected function publishFile(Command $command, string $view, string $destination, array $data, bool $updateMode): void
    {
        // Render the Blade template
        $content = View::make($view, $data)->render();

        // Check if file exists
        if (file_exists($destination)) {
            if ($updateMode) {
                if ($command->option('force')) {
                    $this->writeFile($destination, $content);
                    $command->info("✓ Updated: {$destination}");

                    return;
                }

                $command->warn("File exists: {$destination}");

                $choice = $command->choice(
                    'What would you like to do?',
                    ['Skip', 'Overwrite', 'Show diff', 'Backup and overwrite'],
                    0
                );

                switch ($choice) {
                    case 'Skip':
                        $command->line('  Skipped.');

                        return;

                    case 'Show diff':
                        $this->showDiff($command, $destination, $content);
                        if (! $command->confirm('Overwrite?', false)) {
                            return;
                        }
                        break;

                    case 'Backup and overwrite':
                        $backup = $destination.'.backup-'.date('YmdHis');
                        copy($destination, $backup);
                        $command->info("  Backed up to: {$backup}");
                        break;
                }
            } else {
                // In init mode, just overwrite
                $this->writeFile($destination, $content);
                $command->info("✓ Created: {$destination}");

                return;
            }
        }

        // Write the file
        $this->writeFile($destination, $content);
        $command->info("✓ Created: {$destination}");
    }

    protected function writeFile(string $path, string $content): void
    {
        // Ensure directory exists
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $content);
    }

    protected function showDiff(Command $command, string $file, string $newContent): void
    {
        $old = file_get_contents($file);

        $command->newLine();
        $command->line('<fg=red>--- Current</>');
        $command->line('<fg=green>+++ New</>');
        $command->newLine();

        // Show first 30 lines of diff
        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $newContent);

        $maxLines = min(30, max(count($oldLines), count($newLines)));

        for ($i = 0; $i < $maxLines; $i++) {
            $oldLine = $oldLines[$i] ?? '';
            $newLine = $newLines[$i] ?? '';

            if ($oldLine !== $newLine) {
                if ($oldLine !== '') {
                    $command->line("<fg=red>- {$oldLine}</>");
                }
                if ($newLine !== '') {
                    $command->line("<fg=green>+ {$newLine}</>");
                }
            }
        }

        if ($maxLines < max(count($oldLines), count($newLines))) {
            $command->line('<fg=gray>... (diff truncated)</>');
        }

        $command->newLine();
    }
}
