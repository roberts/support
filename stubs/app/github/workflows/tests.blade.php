name: tests

on:
  push:
    branches:
      - develop
      - main
  pull_request:
    branches:
      - develop
      - main

jobs:
  ci:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout
        uses: actions/checkout@v5

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.4
          tools: composer:v2
          coverage: xdebug

      - name: Setup Node
        uses: actions/setup-node@v5
        with:
          node-version: '22'
          cache: 'npm'

      - name: Install Node Dependencies
        run: npm i

@if($hasFlux ?? false)
      - name: Add Flux Credentials
        run: composer config http-basic.composer.fluxui.dev "${'${{ secrets.FLUX_USERNAME }}'}" "${'${{ secrets.FLUX_LICENSE_KEY }}'}"

@endif
      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist --optimize-autoloader

      - name: Copy Environment File
        run: cp .env.example .env

      - name: Generate Application Key
        run: php artisan key:generate

      - name: Build Assets
        run: npm run build

      - name: Run Tests
        run: composer test
