<?php

use Roberts\Support\Enums\ApprovalStatus;
use Roberts\Support\Tests\Fixtures\TestApprovalModel;

it('casts the status to an enum', function () {
    $model = TestApprovalModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(ApprovalStatus::class);
});

it('sets a default status', function () {
    $model = TestApprovalModel::create(['name' => 'test']);

    expect($model->status)->toBe(ApprovalStatus::PENDING);
});

it('gets the status', function () {
    $model = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::SUBMITTED]);

    expect($model->getStatus())->toBe(ApprovalStatus::SUBMITTED);
});

it('sets the status', function () {
    $model = TestApprovalModel::create(['name' => 'test']);
    $model->setStatus(ApprovalStatus::SUBMITTED);

    expect($model->status)->toBe(ApprovalStatus::SUBMITTED);
});

it('can submit', function () {
    $model = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::PENDING]);
    $model->submit();

    expect($model->status)->toBe(ApprovalStatus::SUBMITTED);
});

it('can approve', function () {
    $model = TestApprovalModel::create(['name' => 'test']);
    $model->approve();

    expect($model->status)->toBe(ApprovalStatus::APPROVED);
});

it('can reject', function () {
    $model = TestApprovalModel::create(['name' => 'test']);
    $model->reject();

    expect($model->status)->toBe(ApprovalStatus::REJECTED);
});

it('checks if it is submitted', function () {
    $model = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::SUBMITTED]);

    expect($model->isSubmitted())->toBeTrue();
    expect($model->isApproved())->toBeFalse();
});

it('checks if it is approved', function () {
    $model = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::APPROVED]);

    expect($model->isApproved())->toBeTrue();
    expect($model->isSubmitted())->toBeFalse();
});

it('checks if it is rejected', function () {
    $model = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::REJECTED]);

    expect($model->isRejected())->toBeTrue();
    expect($model->isSubmitted())->toBeFalse();
});

it('checks if it is pending', function () {
    $model = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::PENDING]);

    expect($model->isPending())->toBeTrue();
    expect($model->isSubmitted())->toBeFalse();
});

it('checks if it is decided', function () {
    $approved = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::APPROVED]);
    $rejected = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::REJECTED]);
    $submitted = TestApprovalModel::create(['name' => 'test', 'status' => ApprovalStatus::SUBMITTED]);

    expect($approved->isDecided())->toBeTrue();
    expect($rejected->isDecided())->toBeTrue();
    expect($submitted->isDecided())->toBeFalse();
});

it('has a submitted scope', function () {
    TestApprovalModel::create(['name' => 'submitted-1', 'status' => ApprovalStatus::SUBMITTED]);
    TestApprovalModel::create(['name' => 'submitted-2', 'status' => ApprovalStatus::SUBMITTED]);
    TestApprovalModel::create(['name' => 'approved-1', 'status' => ApprovalStatus::APPROVED]);

    $models = TestApprovalModel::submitted()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['submitted-1', 'submitted-2']);
});

it('has an approved scope', function () {
    TestApprovalModel::create(['name' => 'approved-1', 'status' => ApprovalStatus::APPROVED]);
    TestApprovalModel::create(['name' => 'approved-2', 'status' => ApprovalStatus::APPROVED]);
    TestApprovalModel::create(['name' => 'submitted-1', 'status' => ApprovalStatus::SUBMITTED]);

    $models = TestApprovalModel::approved()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['approved-1', 'approved-2']);
});

it('has a rejected scope', function () {
    TestApprovalModel::create(['name' => 'rejected-1', 'status' => ApprovalStatus::REJECTED]);
    TestApprovalModel::create(['name' => 'rejected-2', 'status' => ApprovalStatus::REJECTED]);
    TestApprovalModel::create(['name' => 'submitted-1', 'status' => ApprovalStatus::SUBMITTED]);

    $models = TestApprovalModel::rejected()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['rejected-1', 'rejected-2']);
});

it('has a pending scope', function () {
    TestApprovalModel::create(['name' => 'submitted-1', 'status' => ApprovalStatus::SUBMITTED]);
    TestApprovalModel::create(['name' => 'approved-1', 'status' => ApprovalStatus::APPROVED]);

    $models = TestApprovalModel::pending()->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('submitted-1');
});

it('has a decided scope', function () {
    TestApprovalModel::create(['name' => 'approved-1', 'status' => ApprovalStatus::APPROVED]);
    TestApprovalModel::create(['name' => 'rejected-1', 'status' => ApprovalStatus::REJECTED]);
    TestApprovalModel::create(['name' => 'submitted-1', 'status' => ApprovalStatus::SUBMITTED]);

    $models = TestApprovalModel::decided()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['approved-1', 'rejected-1']);
});

it('has a whereStatus scope', function () {
    TestApprovalModel::create(['name' => 'submitted-1', 'status' => ApprovalStatus::SUBMITTED]);
    TestApprovalModel::create(['name' => 'approved-1', 'status' => ApprovalStatus::APPROVED]);

    $models = TestApprovalModel::whereStatus(ApprovalStatus::APPROVED)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('approved-1');
});
