# Laravel Support Package Scaffolding

## Quick Start

After installing `roberts/support` in your Laravel project or package:

```bash
composer require roberts/support
```

### Initialize Your Project

Run the scaffolding command:

```bash
composer support:scaffold
```

This will:
1. Auto-detect if you have a Laravel app or package
2. Detect your project's features (Flux, Filament, Twitter API, Mail, etc.)
3. Prompt for Google Cloud configuration (apps only)
4. Generate and publish all necessary files

### Update Existing Files

To update your scaffolding files to the latest version:

```bash
composer support:update
```

You'll be prompted for each existing file:
- **Skip** - Keep current file
- **Overwrite** - Replace with new version
- **Show diff** - See what changed
- **Backup and overwrite** - Save current file with timestamp

## What Gets Published

### For Laravel Apps

**GitHub Workflows:**
- `tests.yml` - Run PHPUnit/Pest tests
- `phpstan.yml` - Static analysis  
- `lint.yml` - Laravel Pint code style
- `deploy.yml` - Google Cloud Run deployment

**Docker Files:**
- `Dockerfile` - Multi-stage build for Cloud Run
- `.dockerignore` - Optimize build context
- `docker-entrypoint.sh` - Container startup script
- `.gcloudignore` - Optimize Cloud builds

**Configuration:**
- `phpstan.neon.dist` - PHPStan configuration
- `.vscode/settings.json` - VS Code workspace settings
- `.vscode/extensions.json` - Recommended extensions

### For Laravel Packages

**GitHub Workflows:**
- `run-tests.yml` - Matrix testing (PHP 8.4, Laravel 12)
- `phpstan.yml` - Static analysis
- `fix-php-code-style-issues.yml` - Auto-fix code style

**Configuration:**
- `phpstan.neon.dist` - Package-specific PHPStan config

## Features

### Smart Detection

The package automatically detects:
- ✅ Project type (app vs package)
- ✅ Livewire Flux usage
- ✅ Filament usage
- ✅ Twitter/X API integration
- ✅ Mail configuration
- ✅ Queue, cache, session drivers

### Dynamic Templates

Templates adapt based on detected features:
- Flux credentials only added if Flux is detected
- Twitter/Mail secrets only included if configured
- Conditional workflow steps based on project needs

### Best Practices Included

**Security:**
- Non-root Docker user
- Google Secret Manager integration
- GitHub Secrets for sensitive CI/CD data

**Performance:**
- Multi-stage Docker builds
- OPcache optimization
- Laravel caching (config, routes, events)
- Asset building in CI

**Reliability:**
- Health checks
- Database migration locking
- Deployment conditionals (DEPLOYMENT_ENABLED flag)

## Google Cloud Run Deployment

After running `composer support:scaffold` on an app, follow the checklist:

### 1. GitHub Secrets
```bash
gh secret set GCP_PROJECT_ID --body="your-project-id"
gh secret set GCP_WORKLOAD_IDENTITY_PROVIDER --body="projects/..."
gh secret set GCP_SERVICE_ACCOUNT --body="github-actions@..."
gh secret set CLOUD_SQL_CONNECTION_NAME --body="project:region:instance"
```

### 2. GitHub Variables
```bash
gh variable set DEPLOYMENT_ENABLED --body="true"
```

### 3. Google Cloud Secret Manager

Create secrets for your Laravel environment:
```bash
echo -n "your-app-key" | gcloud secrets create APP_KEY --data-file=-
echo -n "mysql" | gcloud secrets create DB_CONNECTION --data-file=-
# ... etc for all Laravel secrets
```

### 4. Google Cloud Resources

- Create Artifact Registry repository
- Create Cloud SQL MySQL instance
- Set up Workload Identity Federation
- Configure service account permissions

## Customization

All templates are Blade files in the package's `stubs/` directory. You can:

1. Fork the package and modify templates
2. Use `composer support:update` to apply changes
3. Selectively overwrite files as needed

## Troubleshooting

**"Context access might be invalid" warnings in VS Code:**
These are normal - GitHub Actions can't validate secrets that don't exist locally. The workflows will run fine once secrets are added to GitHub.

**Docker build fails:**
Ensure your `.env.example` file exists and has all required variables.

**Migrations fail on deployment:**
Check that Cloud SQL connection is configured correctly and the database exists.

## Requirements

- PHP 8.4+
- Laravel 12+
- Git repository with GitHub
- Google Cloud account (for app deployments)

## License

MIT License - see [LICENSE.md](LICENSE.md)
