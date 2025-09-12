<?php

use Roberts\Support\Tests\TestCase;

// Only apply the Laravel TestCase to tests that actually need the framework.
// Leaving the root (e.g. ArchTest) without the application bootstrap prevents
// issues with error handler re-registration (PHPUnit 11 incompat) during arch scans.
uses(TestCase::class)->in(__DIR__.'/Feature', __DIR__.'/Unit', __DIR__.'/Database');
