# Git
.git
.github
.gitignore
.gitattributes

# IDE
.idea
.vscode
*.swp
*.swo
*~

# Environment & Config
.env
.env.*
!.env.example

# Dependencies
node_modules
vendor

# Build artifacts
public/hot
public/storage
public/build

# Tests
tests
*.Test.php
*.test.php
.phpunit.result.cache
phpunit.xml
phpunit.xml.dist
.php-cs-fixer.cache
.php_cs.cache

# Static analysis
phpstan.neon.dist
phpstan-baseline.neon
psalm.xml.dist

# Storage
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
storage/logs/*
storage/app/public/*
storage/debugbar

# Documentation
README.md
CHANGELOG.md
CONTRIBUTING.md
LICENSE
SECURITY.md
*.md

# Development
Homestead.json
Homestead.yaml
Vagrantfile
docker-compose.yml
docker-compose.*.yml
.sail

# OS
.DS_Store
Thumbs.db

# CI/CD
.editorconfig

# NPM/Node
package-lock.json
npm-debug.log
yarn-error.log
yarn.lock
pnpm-lock.yaml
