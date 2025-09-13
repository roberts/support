<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Roberts\Support\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');
