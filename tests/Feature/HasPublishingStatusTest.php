<?php

use Roberts\Support\Enums\PublishingStatus;
use Roberts\Support\Tests\Fixtures\TestPublishingModel;

it('casts the status to an enum', function () {
    $model = TestPublishingModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(PublishingStatus::class);
});

it('sets a default status', function () {
    $model = TestPublishingModel::create(['name' => 'test']);

    expect($model->status)->toBe(PublishingStatus::DRAFT);
});

it('gets the status', function () {
    $model = TestPublishingModel::create(['name' => 'test', 'status' => PublishingStatus::PUBLISHED]);

    expect($model->getStatus())->toBe(PublishingStatus::PUBLISHED);
});

it('sets the status', function () {
    $model = TestPublishingModel::create(['name' => 'test']);
    $model->setStatus(PublishingStatus::PUBLISHED);

    expect($model->status)->toBe(PublishingStatus::PUBLISHED);
});

it('can publish', function () {
    $model = TestPublishingModel::create(['name' => 'test']);
    $model->publish();

    expect($model->status)->toBe(PublishingStatus::PUBLISHED);
});

it('can unpublish', function () {
    $model = TestPublishingModel::create(['name' => 'test', 'status' => PublishingStatus::PUBLISHED]);
    $model->unpublish();

    expect($model->status)->toBe(PublishingStatus::DRAFT);
});

it('can archive', function () {
    $model = TestPublishingModel::create(['name' => 'test']);
    $model->archive();

    expect($model->status)->toBe(PublishingStatus::ARCHIVED);
});

it('checks if it is draft', function () {
    $model = TestPublishingModel::create(['name' => 'test', 'status' => PublishingStatus::DRAFT]);

    expect($model->isDraft())->toBeTrue();
    expect($model->isPublished())->toBeFalse();
});

it('checks if it is published', function () {
    $model = TestPublishingModel::create(['name' => 'test', 'status' => PublishingStatus::PUBLISHED]);

    expect($model->isPublished())->toBeTrue();
    expect($model->isDraft())->toBeFalse();
});

it('checks if it is archived', function () {
    $model = TestPublishingModel::create(['name' => 'test', 'status' => PublishingStatus::ARCHIVED]);

    expect($model->isArchived())->toBeTrue();
    expect($model->isDraft())->toBeFalse();
});

it('has a draft scope', function () {
    TestPublishingModel::create(['name' => 'draft-1', 'status' => PublishingStatus::DRAFT]);
    TestPublishingModel::create(['name' => 'draft-2', 'status' => PublishingStatus::DRAFT]);
    TestPublishingModel::create(['name' => 'published-1', 'status' => PublishingStatus::PUBLISHED]);

    $models = TestPublishingModel::draft()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['draft-1', 'draft-2']);
});

it('has a published scope', function () {
    TestPublishingModel::create(['name' => 'published-1', 'status' => PublishingStatus::PUBLISHED]);
    TestPublishingModel::create(['name' => 'published-2', 'status' => PublishingStatus::PUBLISHED]);
    TestPublishingModel::create(['name' => 'draft-1', 'status' => PublishingStatus::DRAFT]);

    $models = TestPublishingModel::published()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['published-1', 'published-2']);
});

it('has an archived scope', function () {
    TestPublishingModel::create(['name' => 'archived-1', 'status' => PublishingStatus::ARCHIVED]);
    TestPublishingModel::create(['name' => 'archived-2', 'status' => PublishingStatus::ARCHIVED]);
    TestPublishingModel::create(['name' => 'draft-1', 'status' => PublishingStatus::DRAFT]);

    $models = TestPublishingModel::archived()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['archived-1', 'archived-2']);
});

it('has a whereStatus scope', function () {
    TestPublishingModel::create(['name' => 'draft-1', 'status' => PublishingStatus::DRAFT]);
    TestPublishingModel::create(['name' => 'published-1', 'status' => PublishingStatus::PUBLISHED]);

    $models = TestPublishingModel::whereStatus(PublishingStatus::PUBLISHED)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('published-1');
});
