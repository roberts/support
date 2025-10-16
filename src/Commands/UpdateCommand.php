<?php

declare(strict_types=1);

namespace Roberts\Support\Commands;

use Illuminate\Console\Command;
use Roberts\Support\Services\FeatureDetector;
use Roberts\Support\Services\ProjectDetector;
use Roberts\Support\Services\StubPublisher;

class UpdateCommand extends Command
{
    protected $signature = 'support:update {--force : Overwrite all files without prompting}';

    protected $description = 'Update scaffolding files to the latest version from roberts/support package';

    public function handle(
        ProjectDetector $detector,
        FeatureDetector $featureDetector,
        StubPublisher $publisher
    ): int {
        $this->info('🔄 Updating Laravel Support scaffolding...');
        $this->newLine();

        // Detect project type
        $type = $detector->detectProjectType($this);
        $features = $featureDetector->detect();

        $this->info("Detected: Laravel {$type}");
        $this->newLine();

        if ($type === 'app') {
            // Try to read existing config from files
            $config = $this->detectExistingConfig();

            if (! $config) {
                $this->warn('Could not detect existing configuration.');
                if (! $this->confirm('Would you like to reconfigure?', true)) {
                    return self::FAILURE;
                }

                $config = $this->gatherGCloudConfig();
            }

            $publisher->publishAppFiles($this, $config, $features, updateMode: true);
        } else {
            $publisher->publishPackageFiles($this, updateMode: true);
        }

        $this->newLine();
        $this->info('✅ Update complete!');

        return self::SUCCESS;
    }

    protected function detectExistingConfig(): ?array
    {
        $deployFile = base_path('.github/workflows/deploy.yml');

        if (! file_exists($deployFile)) {
            return null;
        }

        $content = file_get_contents($deployFile);

        // Try to extract config from existing file
        preg_match('/PROJECT_ID:.*?[\'"](.*?)[\'"]/', $content, $projectMatches);
        preg_match('/SERVICE_NAME:.*?[\'"](.*?)[\'"]/', $content, $serviceMatches);
        preg_match('/REGION:.*?[\'"](.*?)[\'"]/', $content, $regionMatches);

        if (empty($projectMatches) && empty($serviceMatches)) {
            return null;
        }

        return [
            'projectId' => $projectMatches[1] ?? 'my-project',
            'serviceName' => $serviceMatches[1] ?? basename(base_path()),
            'region' => $regionMatches[1] ?? 'us-central1',
        ];
    }

    protected function gatherGCloudConfig(): array
    {
        $composerName = $this->getComposerName();
        $projectName = basename(base_path());

        return [
            'projectId' => $this->ask(
                'GCP Project ID',
                str_replace('/', '-', $composerName)
            ),
            'serviceName' => $this->ask(
                'Cloud Run service name',
                $projectName
            ),
            'region' => $this->choice(
                'GCP Region',
                ['us-central1', 'us-east1', 'us-west1', 'europe-west1', 'asia-northeast1'],
                0
            ),
        ];
    }

    protected function getComposerName(): string
    {
        $composer = json_decode(
            file_get_contents(base_path('composer.json')),
            true
        );

        return $composer['name'] ?? 'my-project';
    }
}
