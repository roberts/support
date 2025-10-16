name: PHPStan

on:
  push:
    paths:
      - '**.php'
      - 'phpstan.neon.dist'
      - '.github/workflows/phpstan.yml'

jobs:
  phpstan:
    name: phpstan
    runs-on: ubuntu-latest
    timeout-minutes: 5
    steps:
      - uses: actions/checkout@v5

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          coverage: none

@if($hasFlux ?? false)
      - name: Add Flux Credentials
        run: composer config http-basic.composer.fluxui.dev "${'${{ secrets.FLUX_USERNAME }}'}" "${'${{ secrets.FLUX_LICENSE_KEY }}'}"

@endif
      - name: Install composer dependencies
        uses: ramsey/composer-install@v3

      - name: Run PHPStan
        run: ./vendor/bin/phpstan --error-format=github
