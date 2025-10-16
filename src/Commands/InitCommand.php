<?php

declare(strict_types=1);

namespace Roberts\Support\Commands;

use Illuminate\Console\Command;
use Roberts\Support\Services\FeatureDetector;
use Roberts\Support\Services\ProjectDetector;
use Roberts\Support\Services\StubPublisher;

class InitCommand extends Command
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
            // Gather GCloud configuration
            $this->config = $this->gatherGCloudConfig();

            // Show configuration summary
            $this->displayConfiguration($this->config, $this->features);

            if (! $this->confirm('Proceed with these settings?', true)) {
                $this->warn('Cancelled.');

                return self::FAILURE;
            }

            $publisher->publishAppFiles($this, $this->config, $this->features);
        } else {
            $publisher->publishPackageFiles($this);
        }

        $this->displayNextSteps($type);

        return self::SUCCESS;
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

    protected function displayConfiguration(array $config, array $features): void
    {
        $this->newLine();
        $this->table(
            ['Setting', 'Value'],
            [
                ['Project ID', $config['projectId']],
                ['Service Name', $config['serviceName']],
                ['Region', $config['region']],
                ['Database', 'MySQL (Cloud SQL)'],
            ]
        );

        if (count(array_filter($features)) > 0) {
            $this->newLine();
            $this->line('<fg=yellow>Detected Features:</>');
            foreach ($features as $feature => $enabled) {
                if ($enabled) {
                    $this->line('  ✓ '.ucfirst(str_replace('has', '', $feature)));
                }
            }
        }

        $this->newLine();
    }

    protected function displayNextSteps(string $type): void
    {
        $this->newLine();
        $this->info('✅ Files published successfully!');
        $this->newLine(2);

        if ($type === 'app' && $this->config) {
            $this->displayAppNextSteps($this->config, $this->features);
        } else {
            $this->displayPackageNextSteps();
        }
    }

    protected function displayAppNextSteps(array $config, array $features): void
    {
        $this->line('<fg=cyan>═══════════════════════════════════════════════════════════════</>');
        $this->line('<fg=yellow;options=bold>  📋 SETUP CHECKLIST</>');
        $this->line('<fg=cyan>═══════════════════════════════════════════════════════════════</>');
        $this->newLine();

        // GitHub Secrets
        $this->line('<fg=green;options=bold>1. GitHub Secrets</> <fg=gray>(Settings → Secrets and variables → Actions → Secrets)</>');
        $this->newLine();
        $this->line('   <fg=yellow>Required for deployment:</>');
        $this->line('   • <fg=white>GCP_PROJECT_ID</> = <fg=cyan>'.$config['projectId'].'</>');
        $this->line('   • <fg=white>GCP_WORKLOAD_IDENTITY_PROVIDER</> = projects/PROJECT_NUM/locations/global/...');
        $this->line('   • <fg=white>GCP_SERVICE_ACCOUNT</> = github-actions@'.$config['projectId'].'.iam.gserviceaccount.com');
        $this->line('   • <fg=white>GCP_SERVICE_ACCOUNT_EMAIL</> = (same as above)');
        $this->line('   • <fg=white>CLOUD_SQL_CONNECTION_NAME</> = '.$config['projectId'].':'.$config['region'].':your-db-instance');

        if ($features['hasFlux'] ?? false) {
            $this->newLine();
            $this->line('   <fg=yellow>For Livewire Flux:</>');
            $this->line('   • <fg=white>FLUX_USERNAME</> = your-flux-username');
            $this->line('   • <fg=white>FLUX_LICENSE_KEY</> = your-flux-key');
        }

        $this->newLine();
        $this->line('   <fg=gray>Quick command:</>');
        $this->line('   <fg=white>gh secret set GCP_PROJECT_ID --body="'.$config['projectId'].'"</>');

        $this->newLine(2);

        // GitHub Variables
        $this->line('<fg=green;options=bold>2. GitHub Variables</> <fg=gray>(Settings → Secrets and variables → Actions → Variables)</>');
        $this->newLine();
        $this->line('   • <fg=white>DEPLOYMENT_ENABLED</> = <fg=cyan>true</> <fg=gray>(when ready to deploy)</>');
        $this->line('   • <fg=white>GCP_REGION</> = <fg=cyan>'.$config['region'].'</> <fg=gray>(optional, has default)</>');
        $this->line('   • <fg=white>SERVICE_NAME</> = <fg=cyan>'.$config['serviceName'].'</> <fg=gray>(optional, has default)</>');

        $this->newLine();
        $this->line('   <fg=gray>Quick command:</>');
        $this->line('   <fg=white>gh variable set DEPLOYMENT_ENABLED --body="true"</>');

        $this->newLine(2);

        // Google Cloud Secret Manager
        $this->line('<fg=green;options=bold>3. Google Cloud Secret Manager</> <fg=gray>(Runtime secrets for your app)</>');
        $this->newLine();
        $this->line('   <fg=yellow>Required Laravel secrets:</>');
        $this->line('   • <fg=white>APP_KEY</> <fg=gray>(`php artisan key:generate --show`)</>');
        $this->line('   • <fg=white>DB_CONNECTION</> = mysql');
        $this->line('   • <fg=white>DB_HOST</> = /cloudsql/'.$config['projectId'].':'.$config['region'].':INSTANCE');
        $this->line('   • <fg=white>DB_PORT</> = 3306');
        $this->line('   • <fg=white>DB_DATABASE</> = your_database');
        $this->line('   • <fg=white>DB_USERNAME</> = your_user');
        $this->line('   • <fg=white>DB_PASSWORD</> = your_password');

        if ($features['hasTwitter'] ?? false) {
            $this->newLine();
            $this->line('   <fg=yellow>Twitter/X API:</>');
            $this->line('   • <fg=white>TWITTER_CONSUMER_KEY</>');
            $this->line('   • <fg=white>TWITTER_CONSUMER_SECRET</>');
            $this->line('   • <fg=white>TWITTER_ACCESS_TOKEN</>');
            $this->line('   • <fg=white>TWITTER_ACCESS_TOKEN_SECRET</>');
        }

        if ($features['hasMail'] ?? false) {
            $this->newLine();
            $this->line('   <fg=yellow>Mail Configuration:</>');
            $this->line('   • <fg=white>MAIL_MAILER</> = smtp');
            $this->line('   • <fg=white>MAIL_HOST</>');
            $this->line('   • <fg=white>MAIL_PORT</>');
            $this->line('   • <fg=white>MAIL_USERNAME</>');
            $this->line('   • <fg=white>MAIL_PASSWORD</>');
            $this->line('   • <fg=white>MAIL_FROM_ADDRESS</>');
        }

        $this->newLine();
        $this->line('   <fg=gray>Create secrets with:</>');
        $this->line('   <fg=white>echo -n "your-value" | gcloud secrets create SECRET_NAME --data-file=-</>');
        $this->line('   <fg=gray>Or via console:</>');
        $this->line('   <fg=white>https://console.cloud.google.com/security/secret-manager?project='.$config['projectId'].'</>');

        $this->newLine(2);

        // Google Cloud Resources
        $this->line('<fg=green;options=bold>4. Google Cloud Resources</>');
        $this->newLine();
        $this->line('   • Create Artifact Registry repository');
        $this->line('   • Create Cloud SQL MySQL instance');
        $this->line('   • Set up Workload Identity Federation');
        $this->line('   • Configure service account permissions');

        $this->newLine(2);
        $this->line('<fg=cyan>═══════════════════════════════════════════════════════════════</>');
        $this->newLine();
        $this->comment('📚 Full documentation: https://github.com/drewroberts/support#cloud-run');
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
