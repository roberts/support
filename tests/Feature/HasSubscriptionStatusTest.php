<?php

use Roberts\Support\Enums\SubscriptionStatus;
use Roberts\Support\Tests\Fixtures\TestSubscriptionModel;

it('casts the status to an enum', function () {
    $model = TestSubscriptionModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(SubscriptionStatus::class);
});

it('sets a default status', function () {
    $model = TestSubscriptionModel::create(['name' => 'test']);

    expect($model->status)->toBe(SubscriptionStatus::TRIAL);
});

it('gets the status', function () {
    $model = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::ACTIVE]);

    expect($model->getStatus())->toBe(SubscriptionStatus::ACTIVE);
});

it('sets the status', function () {
    $model = TestSubscriptionModel::create(['name' => 'test']);
    $model->setStatus(SubscriptionStatus::ACTIVE);

    expect($model->status)->toBe(SubscriptionStatus::ACTIVE);
});

it('can start trial', function () {
    $model = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::ACTIVE]);
    $model->startTrial();

    expect($model->status)->toBe(SubscriptionStatus::TRIAL);
});

it('can activate', function () {
    $model = TestSubscriptionModel::create(['name' => 'test']);
    $model->activate();

    expect($model->status)->toBe(SubscriptionStatus::ACTIVE);
});

it('can cancel', function () {
    $model = TestSubscriptionModel::create(['name' => 'test']);
    $model->cancel();

    expect($model->status)->toBe(SubscriptionStatus::CANCELED);
});

it('can expire', function () {
    $model = TestSubscriptionModel::create(['name' => 'test']);
    $model->expire();

    expect($model->status)->toBe(SubscriptionStatus::EXPIRED);
});

it('checks if it is trial', function () {
    $model = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::TRIAL]);

    expect($model->isTrial())->toBeTrue();
    expect($model->isActive())->toBeFalse();
});

it('checks if it is active', function () {
    $model = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::ACTIVE]);

    expect($model->isActive())->toBeTrue();
    expect($model->isTrial())->toBeFalse();
});

it('checks if it is canceled', function () {
    $model = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::CANCELED]);

    expect($model->isCanceled())->toBeTrue();
    expect($model->isTrial())->toBeFalse();
});

it('checks if it is expired', function () {
    $model = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::EXPIRED]);

    expect($model->isExpired())->toBeTrue();
    expect($model->isTrial())->toBeFalse();
});

it('checks if it is valid', function () {
    $trial = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::TRIAL]);
    $active = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::ACTIVE]);
    $canceled = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::CANCELED]);

    expect($trial->isValid())->toBeTrue();
    expect($active->isValid())->toBeTrue();
    expect($canceled->isValid())->toBeFalse();
});

it('checks if it is invalid', function () {
    $canceled = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::CANCELED]);
    $expired = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::EXPIRED]);
    $active = TestSubscriptionModel::create(['name' => 'test', 'status' => SubscriptionStatus::ACTIVE]);

    expect($canceled->isInvalid())->toBeTrue();
    expect($expired->isInvalid())->toBeTrue();
    expect($active->isInvalid())->toBeFalse();
});

it('has a trial scope', function () {
    TestSubscriptionModel::create(['name' => 'trial-1', 'status' => SubscriptionStatus::TRIAL]);
    TestSubscriptionModel::create(['name' => 'trial-2', 'status' => SubscriptionStatus::TRIAL]);
    TestSubscriptionModel::create(['name' => 'active-1', 'status' => SubscriptionStatus::ACTIVE]);

    $models = TestSubscriptionModel::trial()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['trial-1', 'trial-2']);
});

it('has an active scope', function () {
    TestSubscriptionModel::create(['name' => 'active-1', 'status' => SubscriptionStatus::ACTIVE]);
    TestSubscriptionModel::create(['name' => 'active-2', 'status' => SubscriptionStatus::ACTIVE]);
    TestSubscriptionModel::create(['name' => 'trial-1', 'status' => SubscriptionStatus::TRIAL]);

    $models = TestSubscriptionModel::active()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['active-1', 'active-2']);
});

it('has a canceled scope', function () {
    TestSubscriptionModel::create(['name' => 'canceled-1', 'status' => SubscriptionStatus::CANCELED]);
    TestSubscriptionModel::create(['name' => 'canceled-2', 'status' => SubscriptionStatus::CANCELED]);
    TestSubscriptionModel::create(['name' => 'trial-1', 'status' => SubscriptionStatus::TRIAL]);

    $models = TestSubscriptionModel::canceled()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['canceled-1', 'canceled-2']);
});

it('has an expired scope', function () {
    TestSubscriptionModel::create(['name' => 'expired-1', 'status' => SubscriptionStatus::EXPIRED]);
    TestSubscriptionModel::create(['name' => 'expired-2', 'status' => SubscriptionStatus::EXPIRED]);
    TestSubscriptionModel::create(['name' => 'trial-1', 'status' => SubscriptionStatus::TRIAL]);

    $models = TestSubscriptionModel::expired()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['expired-1', 'expired-2']);
});

it('has a valid scope', function () {
    TestSubscriptionModel::create(['name' => 'trial-1', 'status' => SubscriptionStatus::TRIAL]);
    TestSubscriptionModel::create(['name' => 'active-1', 'status' => SubscriptionStatus::ACTIVE]);
    TestSubscriptionModel::create(['name' => 'canceled-1', 'status' => SubscriptionStatus::CANCELED]);

    $models = TestSubscriptionModel::valid()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['trial-1', 'active-1']);
});

it('has an invalid scope', function () {
    TestSubscriptionModel::create(['name' => 'canceled-1', 'status' => SubscriptionStatus::CANCELED]);
    TestSubscriptionModel::create(['name' => 'expired-1', 'status' => SubscriptionStatus::EXPIRED]);
    TestSubscriptionModel::create(['name' => 'active-1', 'status' => SubscriptionStatus::ACTIVE]);

    $models = TestSubscriptionModel::invalid()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['canceled-1', 'expired-1']);
});

it('has a whereStatus scope', function () {
    TestSubscriptionModel::create(['name' => 'trial-1', 'status' => SubscriptionStatus::TRIAL]);
    TestSubscriptionModel::create(['name' => 'active-1', 'status' => SubscriptionStatus::ACTIVE]);

    $models = TestSubscriptionModel::whereStatus(SubscriptionStatus::ACTIVE)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('active-1');
});
