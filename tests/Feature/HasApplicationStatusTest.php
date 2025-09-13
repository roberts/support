<?php

use Roberts\Support\Enums\ApplicationStatus;
use Roberts\Support\Tests\Fixtures\TestApplicationModel;

it('casts the status to an enum', function () {
    $model = TestApplicationModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(ApplicationStatus::class);
});

it('sets a default status', function () {
    $model = TestApplicationModel::create(['name' => 'test']);

    expect($model->status)->toBe(ApplicationStatus::STARTED);
});

it('gets the status', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::APPLIED]);

    expect($model->getStatus())->toBe(ApplicationStatus::APPLIED);
});

it('sets the status', function () {
    $model = TestApplicationModel::create(['name' => 'test']);
    $model->setStatus(ApplicationStatus::APPLIED);

    expect($model->status)->toBe(ApplicationStatus::APPLIED);
});

it('can start', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::APPLIED]);
    $model->start();

    expect($model->status)->toBe(ApplicationStatus::STARTED);
});

it('can verify', function () {
    $model = TestApplicationModel::create(['name' => 'test']);
    $model->verify();

    expect($model->status)->toBe(ApplicationStatus::VERIFIED);
});

it('can apply', function () {
    $model = TestApplicationModel::create(['name' => 'test']);
    $model->apply();

    expect($model->status)->toBe(ApplicationStatus::APPLIED);
});

it('can accept', function () {
    $model = TestApplicationModel::create(['name' => 'test']);
    $model->accept();

    expect($model->status)->toBe(ApplicationStatus::ACCEPTED);
});

it('can reject', function () {
    $model = TestApplicationModel::create(['name' => 'test']);
    $model->reject();

    expect($model->status)->toBe(ApplicationStatus::REJECTED);
});

it('checks if it is started', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::STARTED]);

    expect($model->isStarted())->toBeTrue();
    expect($model->isApplied())->toBeFalse();
});

it('checks if it is verified', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::VERIFIED]);

    expect($model->isVerified())->toBeTrue();
    expect($model->isStarted())->toBeFalse();
});

it('checks if it is applied', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::APPLIED]);

    expect($model->isApplied())->toBeTrue();
    expect($model->isStarted())->toBeFalse();
});

it('checks if it is accepted', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::ACCEPTED]);

    expect($model->isAccepted())->toBeTrue();
    expect($model->isStarted())->toBeFalse();
});

it('checks if it is rejected', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::REJECTED]);

    expect($model->isRejected())->toBeTrue();
    expect($model->isStarted())->toBeFalse();
});

it('checks if it is pending', function () {
    $model = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::PENDING]);

    expect($model->isPending())->toBeTrue();
    expect($model->isStarted())->toBeFalse();
});

it('checks if it is decided', function () {
    $accepted = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::ACCEPTED]);
    $rejected = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::REJECTED]);
    $started = TestApplicationModel::create(['name' => 'test', 'status' => ApplicationStatus::STARTED]);

    expect($accepted->isDecided())->toBeTrue();
    expect($rejected->isDecided())->toBeTrue();
    expect($started->isDecided())->toBeFalse();
});

it('has a started scope', function () {
    TestApplicationModel::create(['name' => 'started-1', 'status' => ApplicationStatus::STARTED]);
    TestApplicationModel::create(['name' => 'started-2', 'status' => ApplicationStatus::STARTED]);
    TestApplicationModel::create(['name' => 'applied-1', 'status' => ApplicationStatus::APPLIED]);

    $models = TestApplicationModel::started()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['started-1', 'started-2']);
});

it('has a verified scope', function () {
    TestApplicationModel::create(['name' => 'verified-1', 'status' => ApplicationStatus::VERIFIED]);
    TestApplicationModel::create(['name' => 'verified-2', 'status' => ApplicationStatus::VERIFIED]);
    TestApplicationModel::create(['name' => 'applied-1', 'status' => ApplicationStatus::APPLIED]);

    $models = TestApplicationModel::verified()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['verified-1', 'verified-2']);
});

it('has an applied scope', function () {
    TestApplicationModel::create(['name' => 'applied-1', 'status' => ApplicationStatus::APPLIED]);
    TestApplicationModel::create(['name' => 'applied-2', 'status' => ApplicationStatus::APPLIED]);
    TestApplicationModel::create(['name' => 'started-1', 'status' => ApplicationStatus::STARTED]);

    $models = TestApplicationModel::applied()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['applied-1', 'applied-2']);
});

it('has an accepted scope', function () {
    TestApplicationModel::create(['name' => 'accepted-1', 'status' => ApplicationStatus::ACCEPTED]);
    TestApplicationModel::create(['name' => 'accepted-2', 'status' => ApplicationStatus::ACCEPTED]);
    TestApplicationModel::create(['name' => 'started-1', 'status' => ApplicationStatus::STARTED]);

    $models = TestApplicationModel::accepted()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['accepted-1', 'accepted-2']);
});

it('has a rejected scope', function () {
    TestApplicationModel::create(['name' => 'rejected-1', 'status' => ApplicationStatus::REJECTED]);
    TestApplicationModel::create(['name' => 'rejected-2', 'status' => ApplicationStatus::REJECTED]);
    TestApplicationModel::create(['name' => 'started-1', 'status' => ApplicationStatus::STARTED]);

    $models = TestApplicationModel::rejected()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['rejected-1', 'rejected-2']);
});

it('has a pending scope', function () {
    TestApplicationModel::create(['name' => 'started-1', 'status' => ApplicationStatus::STARTED]);
    TestApplicationModel::create(['name' => 'verified-1', 'status' => ApplicationStatus::VERIFIED]);
    TestApplicationModel::create(['name' => 'applied-1', 'status' => ApplicationStatus::APPLIED]);
    TestApplicationModel::create(['name' => 'accepted-1', 'status' => ApplicationStatus::ACCEPTED]);

    $models = TestApplicationModel::pending()->get();

    expect($models)->toHaveCount(3)
        ->and($models->pluck('name')->all())->toBe(['started-1', 'verified-1', 'applied-1']);
});

it('has a decided scope', function () {
    TestApplicationModel::create(['name' => 'accepted-1', 'status' => ApplicationStatus::ACCEPTED]);
    TestApplicationModel::create(['name' => 'rejected-1', 'status' => ApplicationStatus::REJECTED]);
    TestApplicationModel::create(['name' => 'started-1', 'status' => ApplicationStatus::STARTED]);

    $models = TestApplicationModel::decided()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['accepted-1', 'rejected-1']);
});

it('has a whereStatus scope', function () {
    TestApplicationModel::create(['name' => 'started-1', 'status' => ApplicationStatus::STARTED]);
    TestApplicationModel::create(['name' => 'applied-1', 'status' => ApplicationStatus::APPLIED]);

    $models = TestApplicationModel::whereStatus(ApplicationStatus::APPLIED)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('applied-1');
});
