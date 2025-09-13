<?php

use Roberts\Support\Enums\ModeratorStatus;
use Roberts\Support\Tests\Fixtures\TestModeratorModel;

it('casts the status to an enum', function () {
    $model = TestModeratorModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(ModeratorStatus::class);
});

it('sets a default status', function () {
    $model = TestModeratorModel::create(['name' => 'test']);

    expect($model->status)->toBe(ModeratorStatus::PENDING);
});

it('gets the status', function () {
    $model = TestModeratorModel::create(['name' => 'test', 'status' => ModeratorStatus::APPROVED]);

    expect($model->getStatus())->toBe(ModeratorStatus::APPROVED);
});

it('sets the status', function () {
    $model = TestModeratorModel::create(['name' => 'test']);
    $model->setStatus(ModeratorStatus::APPROVED);

    expect($model->status)->toBe(ModeratorStatus::APPROVED);
});

it('can mark as pending', function () {
    $model = TestModeratorModel::create(['name' => 'test', 'status' => ModeratorStatus::APPROVED]);
    $model->markAsPending();

    expect($model->status)->toBe(ModeratorStatus::PENDING);
});

it('can approve', function () {
    $model = TestModeratorModel::create(['name' => 'test']);
    $model->approve();

    expect($model->status)->toBe(ModeratorStatus::APPROVED);
});

it('can reject', function () {
    $model = TestModeratorModel::create(['name' => 'test']);
    $model->reject();

    expect($model->status)->toBe(ModeratorStatus::REJECTED);
});

it('can flag', function () {
    $model = TestModeratorModel::create(['name' => 'test']);
    $model->flag();

    expect($model->status)->toBe(ModeratorStatus::FLAGGED);
});

it('checks if it is pending', function () {
    $model = TestModeratorModel::create(['name' => 'test', 'status' => ModeratorStatus::PENDING]);

    expect($model->isPending())->toBeTrue();
    expect($model->isApproved())->toBeFalse();
});

it('checks if it is approved', function () {
    $model = TestModeratorModel::create(['name' => 'test', 'status' => ModeratorStatus::APPROVED]);

    expect($model->isApproved())->toBeTrue();
    expect($model->isPending())->toBeFalse();
});

it('checks if it is rejected', function () {
    $model = TestModeratorModel::create(['name' => 'test', 'status' => ModeratorStatus::REJECTED]);

    expect($model->isRejected())->toBeTrue();
    expect($model->isPending())->toBeFalse();
});

it('checks if it is flagged', function () {
    $model = TestModeratorModel::create(['name' => 'test', 'status' => ModeratorStatus::FLAGGED]);

    expect($model->isFlagged())->toBeTrue();
    expect($model->isPending())->toBeFalse();
});

it('checks if it is decided', function () {
    $approved = TestModeratorModel::create(['name' => 'test-approved', 'status' => ModeratorStatus::APPROVED]);
    $rejected = TestModeratorModel::create(['name' => 'test-rejected', 'status' => ModeratorStatus::REJECTED]);
    $pending = TestModeratorModel::create(['name' => 'test-pending', 'status' => ModeratorStatus::PENDING]);

    expect($approved->isDecided())->toBeTrue();
    expect($rejected->isDecided())->toBeTrue();
    expect($pending->isDecided())->toBeFalse();
});

it('checks if it needs moderation', function () {
    $pending = TestModeratorModel::create(['name' => 'test-pending', 'status' => ModeratorStatus::PENDING]);
    $flagged = TestModeratorModel::create(['name' => 'test-flagged', 'status' => ModeratorStatus::FLAGGED]);
    $approved = TestModeratorModel::create(['name' => 'test-approved', 'status' => ModeratorStatus::APPROVED]);

    expect($pending->needsModeration())->toBeTrue();
    expect($flagged->needsModeration())->toBeTrue();
    expect($approved->needsModeration())->toBeFalse();
});

it('has a pending scope', function () {
    TestModeratorModel::create(['name' => 'pending-1', 'status' => ModeratorStatus::PENDING]);
    TestModeratorModel::create(['name' => 'pending-2', 'status' => ModeratorStatus::PENDING]);
    TestModeratorModel::create(['name' => 'approved-1', 'status' => ModeratorStatus::APPROVED]);

    $models = TestModeratorModel::pending()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['pending-1', 'pending-2']);
});

it('has an approved scope', function () {
    TestModeratorModel::create(['name' => 'approved-1', 'status' => ModeratorStatus::APPROVED]);
    TestModeratorModel::create(['name' => 'approved-2', 'status' => ModeratorStatus::APPROVED]);
    TestModeratorModel::create(['name' => 'pending-1', 'status' => ModeratorStatus::PENDING]);

    $models = TestModeratorModel::approved()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['approved-1', 'approved-2']);
});

it('has a rejected scope', function () {
    TestModeratorModel::create(['name' => 'rejected-1', 'status' => ModeratorStatus::REJECTED]);
    TestModeratorModel::create(['name' => 'rejected-2', 'status' => ModeratorStatus::REJECTED]);
    TestModeratorModel::create(['name' => 'pending-1', 'status' => ModeratorStatus::PENDING]);

    $models = TestModeratorModel::rejected()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['rejected-1', 'rejected-2']);
});

it('has a flagged scope', function () {
    TestModeratorModel::create(['name' => 'flagged-1', 'status' => ModeratorStatus::FLAGGED]);
    TestModeratorModel::create(['name' => 'flagged-2', 'status' => ModeratorStatus::FLAGGED]);
    TestModeratorModel::create(['name' => 'pending-1', 'status' => ModeratorStatus::PENDING]);

    $models = TestModeratorModel::flagged()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['flagged-1', 'flagged-2']);
});

it('has a decided scope', function () {
    TestModeratorModel::create(['name' => 'approved-1', 'status' => ModeratorStatus::APPROVED]);
    TestModeratorModel::create(['name' => 'rejected-1', 'status' => ModeratorStatus::REJECTED]);
    TestModeratorModel::create(['name' => 'pending-1', 'status' => ModeratorStatus::PENDING]);

    $models = TestModeratorModel::decided()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['approved-1', 'rejected-1']);
});

it('has a needing moderation scope', function () {
    TestModeratorModel::create(['name' => 'pending-1', 'status' => ModeratorStatus::PENDING]);
    TestModeratorModel::create(['name' => 'flagged-1', 'status' => ModeratorStatus::FLAGGED]);
    TestModeratorModel::create(['name' => 'approved-1', 'status' => ModeratorStatus::APPROVED]);

    $models = TestModeratorModel::needingModeration()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['pending-1', 'flagged-1']);
});

it('has a whereStatus scope', function () {
    TestModeratorModel::create(['name' => 'pending-1', 'status' => ModeratorStatus::PENDING]);
    TestModeratorModel::create(['name' => 'approved-1', 'status' => ModeratorStatus::APPROVED]);

    $models = TestModeratorModel::whereStatus(ModeratorStatus::APPROVED)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('approved-1');
});
