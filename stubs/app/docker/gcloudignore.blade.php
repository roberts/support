# .gcloudignore
# This file specifies files that are intentionally untracked by gcloud

# Git
.git
.gitignore
.gitattributes

# GitHub
.github

# IDE
.idea
.vscode
*.swp
*.swo
*~

# Environment files
.env
.env.*
!.env.example

# Node modules
node_modules

# Vendor
vendor

# Tests
tests
*.Test.php
*.test.php
.phpunit.result.cache
phpunit.xml
phpunit.xml.dist

# Static analysis
phpstan.neon.dist
phpstan-baseline.neon
psalm.xml.dist
.php-cs-fixer.cache

# Storage
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
storage/logs/*
storage/app/public/*

# Documentation
*.md
!README.md

# Development
docker-compose.yml
docker-compose.*.yml
.editorconfig

# Build artifacts
public/build/*
public/hot

# OS
.DS_Store
Thumbs.db

# Logs
*.log
npm-debug.log*
yarn-debug.log*
yarn-error.log*
