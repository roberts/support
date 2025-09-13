<?php

use Roberts\Support\Enums\ActiveStatus;
use Roberts\Support\Tests\Fixtures\TestActiveModel;

it('casts the status to an enum', function () {
    $model = TestActiveModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(ActiveStatus::class);
});

it('sets a default status', function () {
    $model = TestActiveModel::create(['name' => 'test']);

    expect($model->status)->toBe(ActiveStatus::ACTIVE);
});

it('gets the status', function () {
    $model = TestActiveModel::create(['name' => 'test', 'status' => ActiveStatus::INACTIVE]);

    expect($model->getStatus())->toBe(ActiveStatus::INACTIVE);
});

it('sets the status', function () {
    $model = TestActiveModel::create(['name' => 'test']);
    $model->setStatus(ActiveStatus::INACTIVE);

    expect($model->status)->toBe(ActiveStatus::INACTIVE);
});

it('can activate', function () {
    $model = TestActiveModel::create(['name' => 'test', 'status' => ActiveStatus::INACTIVE]);
    $model->activate();

    expect($model->status)->toBe(ActiveStatus::ACTIVE);
});

it('can deactivate', function () {
    $model = TestActiveModel::create(['name' => 'test']);
    $model->deactivate();

    expect($model->status)->toBe(ActiveStatus::INACTIVE);
});

it('checks if it is active', function () {
    $activeModel = TestActiveModel::create(['name' => 'test', 'status' => ActiveStatus::ACTIVE]);
    $inactiveModel = TestActiveModel::create(['name' => 'test', 'status' => ActiveStatus::INACTIVE]);

    expect($activeModel->isActive())->toBeTrue();
    expect($inactiveModel->isActive())->toBeFalse();
});

it('checks if it is inactive', function () {
    $activeModel = TestActiveModel::create(['name' => 'test', 'status' => ActiveStatus::ACTIVE]);
    $inactiveModel = TestActiveModel::create(['name' => 'test', 'status' => ActiveStatus::INACTIVE]);

    expect($activeModel->isInactive())->toBeFalse();
    expect($inactiveModel->isInactive())->toBeTrue();
});

it('has an active scope', function () {
    TestActiveModel::create(['name' => 'active-1', 'status' => ActiveStatus::ACTIVE]);
    TestActiveModel::create(['name' => 'active-2', 'status' => ActiveStatus::ACTIVE]);
    TestActiveModel::create(['name' => 'inactive-1', 'status' => ActiveStatus::INACTIVE]);

    $activeModels = TestActiveModel::active()->get();

    expect($activeModels)->toHaveCount(2)
        ->and($activeModels->pluck('name')->all())->toBe(['active-1', 'active-2']);
});

it('has an inactive scope', function () {
    TestActiveModel::create(['name' => 'active-1', 'status' => ActiveStatus::ACTIVE]);
    TestActiveModel::create(['name' => 'inactive-1', 'status' => ActiveStatus::INACTIVE]);
    TestActiveModel::create(['name' => 'inactive-2', 'status' => ActiveStatus::INACTIVE]);

    $inactiveModels = TestActiveModel::inactive()->get();

    expect($inactiveModels)->toHaveCount(2)
        ->and($inactiveModels->pluck('name')->all())->toBe(['inactive-1', 'inactive-2']);
});

it('has a whereStatus scope', function () {
    TestActiveModel::create(['name' => 'active-1', 'status' => ActiveStatus::ACTIVE]);
    TestActiveModel::create(['name' => 'inactive-1', 'status' => ActiveStatus::INACTIVE]);

    $models = TestActiveModel::whereStatus(ActiveStatus::INACTIVE)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('inactive-1');
});
