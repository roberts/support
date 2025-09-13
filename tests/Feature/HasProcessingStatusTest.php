<?php

use Roberts\Support\Enums\ProcessingStatus;
use Roberts\Support\Tests\Fixtures\TestProcessingModel;

it('casts the status to an enum', function () {
    $model = TestProcessingModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(ProcessingStatus::class);
});

it('sets a default status', function () {
    $model = TestProcessingModel::create(['name' => 'test']);

    expect($model->status)->toBe(ProcessingStatus::PENDING);
});

it('gets the status', function () {
    $model = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::PROCESSING]);

    expect($model->getStatus())->toBe(ProcessingStatus::PROCESSING);
});

it('sets the status', function () {
    $model = TestProcessingModel::create(['name' => 'test']);
    $model->setStatus(ProcessingStatus::PROCESSING);

    expect($model->status)->toBe(ProcessingStatus::PROCESSING);
});

it('can mark as pending', function () {
    $model = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::PROCESSING]);
    $model->markAsPending();

    expect($model->status)->toBe(ProcessingStatus::PENDING);
});

it('can mark as processing', function () {
    $model = TestProcessingModel::create(['name' => 'test']);
    $model->markAsProcessing();

    expect($model->status)->toBe(ProcessingStatus::PROCESSING);
});

it('can mark as completed', function () {
    $model = TestProcessingModel::create(['name' => 'test']);
    $model->markAsCompleted();

    expect($model->status)->toBe(ProcessingStatus::COMPLETED);
});

it('can mark as failed', function () {
    $model = TestProcessingModel::create(['name' => 'test']);
    $model->markAsFailed();

    expect($model->status)->toBe(ProcessingStatus::FAILED);
});

it('checks if it is pending', function () {
    $model = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::PENDING]);

    expect($model->isPending())->toBeTrue();
    expect($model->isProcessing())->toBeFalse();
});

it('checks if it is processing', function () {
    $model = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::PROCESSING]);

    expect($model->isProcessing())->toBeTrue();
    expect($model->isPending())->toBeFalse();
});

it('checks if it is completed', function () {
    $model = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::COMPLETED]);

    expect($model->isCompleted())->toBeTrue();
    expect($model->isPending())->toBeFalse();
});

it('checks if it is failed', function () {
    $model = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::FAILED]);

    expect($model->isFailed())->toBeTrue();
    expect($model->isPending())->toBeFalse();
});

it('checks if it is finished', function () {
    $completed = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::COMPLETED]);
    $failed = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::FAILED]);
    $pending = TestProcessingModel::create(['name' => 'test', 'status' => ProcessingStatus::PENDING]);

    expect($completed->isFinished())->toBeTrue();
    expect($failed->isFinished())->toBeTrue();
    expect($pending->isFinished())->toBeFalse();
});

it('has a pending scope', function () {
    TestProcessingModel::create(['name' => 'pending-1', 'status' => ProcessingStatus::PENDING]);
    TestProcessingModel::create(['name' => 'pending-2', 'status' => ProcessingStatus::PENDING]);
    TestProcessingModel::create(['name' => 'processing-1', 'status' => ProcessingStatus::PROCESSING]);

    $models = TestProcessingModel::pending()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['pending-1', 'pending-2']);
});

it('has a processing scope', function () {
    TestProcessingModel::create(['name' => 'processing-1', 'status' => ProcessingStatus::PROCESSING]);
    TestProcessingModel::create(['name' => 'processing-2', 'status' => ProcessingStatus::PROCESSING]);
    TestProcessingModel::create(['name' => 'pending-1', 'status' => ProcessingStatus::PENDING]);

    $models = TestProcessingModel::processing()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['processing-1', 'processing-2']);
});

it('has a completed scope', function () {
    TestProcessingModel::create(['name' => 'completed-1', 'status' => ProcessingStatus::COMPLETED]);
    TestProcessingModel::create(['name' => 'completed-2', 'status' => ProcessingStatus::COMPLETED]);
    TestProcessingModel::create(['name' => 'pending-1', 'status' => ProcessingStatus::PENDING]);

    $models = TestProcessingModel::completed()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['completed-1', 'completed-2']);
});

it('has a failed scope', function () {
    TestProcessingModel::create(['name' => 'failed-1', 'status' => ProcessingStatus::FAILED]);
    TestProcessingModel::create(['name' => 'failed-2', 'status' => ProcessingStatus::FAILED]);
    TestProcessingModel::create(['name' => 'pending-1', 'status' => ProcessingStatus::PENDING]);

    $models = TestProcessingModel::failed()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['failed-1', 'failed-2']);
});

it('has a finished scope', function () {
    TestProcessingModel::create(['name' => 'completed-1', 'status' => ProcessingStatus::COMPLETED]);
    TestProcessingModel::create(['name' => 'failed-1', 'status' => ProcessingStatus::FAILED]);
    TestProcessingModel::create(['name' => 'pending-1', 'status' => ProcessingStatus::PENDING]);

    $models = TestProcessingModel::finished()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['completed-1', 'failed-1']);
});

it('has a whereStatus scope', function () {
    TestProcessingModel::create(['name' => 'pending-1', 'status' => ProcessingStatus::PENDING]);
    TestProcessingModel::create(['name' => 'processing-1', 'status' => ProcessingStatus::PROCESSING]);

    $models = TestProcessingModel::whereStatus(ProcessingStatus::PROCESSING)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('processing-1');
});
