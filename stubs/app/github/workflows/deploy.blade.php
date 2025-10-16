# yaml-language-server: $schema=https://json.schemastore.org/github-workflow.json

name: Deploy to Google Cloud Run

# Required GitHub Secrets:
# - GCP_PROJECT_ID: Google Cloud project ID
# - GCP_WORKLOAD_IDENTITY_PROVIDER: Workload Identity Provider resource name
# - GCP_SERVICE_ACCOUNT: Service account email for deployment
# - GCP_SERVICE_ACCOUNT_EMAIL: Service account email for Cloud Run
# - CLOUD_SQL_CONNECTION_NAME: Cloud SQL connection string (project:region:instance)
@if($hasFlux ?? false)
# - FLUX_USERNAME: Livewire Flux username
# - FLUX_LICENSE_KEY: Livewire Flux license key
@endif
# 
# Required Google Cloud Secret Manager Secrets (for Laravel .env variables):
# - APP_KEY, DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
@if($hasTwitter ?? false)
# - TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET
@endif
@if($hasMail ?? false)
# - MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS
@endif
#
# Required GitHub Variables:
# - DEPLOYMENT_ENABLED: Set to 'true' to enable deployments (Settings → Secrets and variables → Actions → Variables)

on:
  push:
    branches:
      - main
  workflow_dispatch:  # Allow manual triggering

# Required for Google Cloud authentication
permissions:
  contents: read
  id-token: write

env:
  PROJECT_ID: ${'${{ vars.GCP_PROJECT_ID || secrets.GCP_PROJECT_ID || \''.$projectId.'\' }}'}
  REGION: ${'${{ vars.GCP_REGION || \''.$region.'\' }}'}
  SERVICE_NAME: ${'${{ vars.SERVICE_NAME || \''.$serviceName.'\' }}'}
  REGISTRY: ${'${{ vars.GCP_REGISTRY || \''.$region.'-docker.pkg.dev\' }}'}
  REPOSITORY: ${'${{ vars.GCP_REPOSITORY || \''.basename($serviceName).'\' }}'}

jobs:
  # Wait for tests to pass before deploying
  wait-for-checks:
    runs-on: ubuntu-latest
    if: vars.DEPLOYMENT_ENABLED == 'true'
    steps:
      - name: Wait for status checks
        uses: lewagon/wait-on-check-action@v1.3.4
        with:
          ref: ${'${{ github.ref }}'}
          check-regexp: 'tests|phpstan|lint'  # Matches your existing workflows
          repo-token: ${'${{ secrets.GITHUB_TOKEN }}'}
          wait-interval: 10

  deploy:
    needs: wait-for-checks
    runs-on: ubuntu-latest
    environment: production

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Set up Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '22'
          cache: 'npm'

      - name: Install Node dependencies
        run: npm ci --no-audit --no-fund

@if($hasFlux ?? false)
      - name: Add Flux Credentials
        run: composer config http-basic.composer.fluxui.dev "${'${{ secrets.FLUX_USERNAME }}'}" "${'${{ secrets.FLUX_LICENSE_KEY }}'}"

@endif
      - name: Build assets with Vite
        run: npm run build

      - name: Authenticate to Google Cloud
        uses: google-github-actions/auth@v2
        with:
          workload_identity_provider: ${'${{ secrets.GCP_WORKLOAD_IDENTITY_PROVIDER }}'}
          service_account: ${'${{ secrets.GCP_SERVICE_ACCOUNT }}'}

      - name: Set up Cloud SDK
        uses: google-github-actions/setup-gcloud@v2

      - name: Configure Docker for Artifact Registry
        run: gcloud auth configure-docker ${'${{ env.REGISTRY }}'}

      - name: Build Docker image
        run: |
          docker build \
            --tag ${'${{ env.REGISTRY }}'}/${'${{ env.PROJECT_ID }}'}/${'${{ env.REPOSITORY }}'}/${'${{ env.SERVICE_NAME }}'}:${'${{ github.sha }}'} \
            --tag ${'${{ env.REGISTRY }}'}/${'${{ env.PROJECT_ID }}'}/${'${{ env.REPOSITORY }}'}/${'${{ env.SERVICE_NAME }}'}:latest \
            .

      - name: Push Docker image to Artifact Registry
        run: |
          docker push ${'${{ env.REGISTRY }}'}/${'${{ env.PROJECT_ID }}'}/${'${{ env.REPOSITORY }}'}/${'${{ env.SERVICE_NAME }}'}:${'${{ github.sha }}'}
          docker push ${'${{ env.REGISTRY }}'}/${'${{ env.PROJECT_ID }}'}/${'${{ env.REPOSITORY }}'}/${'${{ env.SERVICE_NAME }}'}:latest

      - name: Deploy to Cloud Run
        id: deploy
        uses: google-github-actions/deploy-cloudrun@v2
        with:
          service: ${'${{ env.SERVICE_NAME }}'}
          region: ${'${{ env.REGION }}'}
          image: ${'${{ env.REGISTRY }}'}/${'${{ env.PROJECT_ID }}'}/${'${{ env.REPOSITORY }}'}/${'${{ env.SERVICE_NAME }}'}:${'${{ github.sha }}'}
          
          # Cloud Run configuration
          flags: |
            --port=8080
            --cpu=1
            --memory=1Gi
            --min-instances=1
            --max-instances=10
            --concurrency=80
            --timeout=300
            --allow-unauthenticated
            --service-account=${'${{ secrets.GCP_SERVICE_ACCOUNT_EMAIL }}'}
            --add-cloudsql-instances=${'${{ secrets.CLOUD_SQL_CONNECTION_NAME }}'}
          
          # Environment variables (non-sensitive)
          env_vars: |
            APP_ENV=production
            APP_DEBUG=false
            LOG_CHANNEL=stderr
            LOG_LEVEL=info
            SESSION_DRIVER=database
            CACHE_STORE=database
            QUEUE_CONNECTION=database
            FILESYSTEM_DISK=gcs
          
          # Secrets from Google Secret Manager
          secrets: |
            APP_KEY=APP_KEY:latest
            DB_CONNECTION=DB_CONNECTION:latest
            DB_HOST=DB_HOST:latest
            DB_PORT=DB_PORT:latest
            DB_DATABASE=DB_DATABASE:latest
            DB_USERNAME=DB_USERNAME:latest
            DB_PASSWORD=DB_PASSWORD:latest
@if($hasTwitter ?? false)
            TWITTER_CONSUMER_KEY=TWITTER_CONSUMER_KEY:latest
            TWITTER_CONSUMER_SECRET=TWITTER_CONSUMER_SECRET:latest
            TWITTER_ACCESS_TOKEN=TWITTER_ACCESS_TOKEN:latest
            TWITTER_ACCESS_TOKEN_SECRET=TWITTER_ACCESS_TOKEN_SECRET:latest
@endif
@if($hasMail ?? false)
            MAIL_MAILER=MAIL_MAILER:latest
            MAIL_HOST=MAIL_HOST:latest
            MAIL_PORT=MAIL_PORT:latest
            MAIL_USERNAME=MAIL_USERNAME:latest
            MAIL_PASSWORD=MAIL_PASSWORD:latest
            MAIL_FROM_ADDRESS=MAIL_FROM_ADDRESS:latest
@endif

      - name: Show deployment URL
        run: echo "Deployed to ${'${{ steps.deploy.outputs.url }}'}"

      - name: Verify deployment
        run: |
          curl -f ${'${{ steps.deploy.outputs.url }}'}/up || exit 1
          echo "✅ Health check passed!"

      - name: Notify on failure
        if: failure()
        run: |
          echo "::error::Deployment failed. Check logs for details."
