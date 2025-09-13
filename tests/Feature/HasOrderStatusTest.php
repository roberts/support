<?php

use Roberts\Support\Enums\OrderStatus;
use Roberts\Support\Tests\Fixtures\TestOrderModel;

it('casts the status to an enum', function () {
    $model = TestOrderModel::create(['name' => 'test']);

    expect($model->status)->toBeInstanceOf(OrderStatus::class);
});

it('sets a default status', function () {
    $model = TestOrderModel::create(['name' => 'test']);

    expect($model->status)->toBe(OrderStatus::CART);
});

it('gets the status', function () {
    $model = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::PAID]);

    expect($model->getStatus())->toBe(OrderStatus::PAID);
});

it('sets the status', function () {
    $model = TestOrderModel::create(['name' => 'test']);
    $model->setStatus(OrderStatus::PAID);

    expect($model->status)->toBe(OrderStatus::PAID);
});

it('can move to checkout', function () {
    $model = TestOrderModel::create(['name' => 'test']);
    $model->moveToCheckout();

    expect($model->status)->toBe(OrderStatus::CHECKOUT);
});

it('can mark as paid', function () {
    $model = TestOrderModel::create(['name' => 'test']);
    $model->markAsPaid();

    expect($model->status)->toBe(OrderStatus::PAID);
});

it('can mark as shipped', function () {
    $model = TestOrderModel::create(['name' => 'test']);
    $model->markAsShipped();

    expect($model->status)->toBe(OrderStatus::SHIPPED);
});

it('can mark as delivered', function () {
    $model = TestOrderModel::create(['name' => 'test']);
    $model->markAsDelivered();

    expect($model->status)->toBe(OrderStatus::DELIVERED);
});

it('checks if it is cart', function () {
    $model = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::CART]);

    expect($model->isCart())->toBeTrue();
    expect($model->isPaid())->toBeFalse();
});

it('checks if it is checkout', function () {
    $model = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::CHECKOUT]);

    expect($model->isCheckout())->toBeTrue();
    expect($model->isCart())->toBeFalse();
});

it('checks if it is paid', function () {
    $model = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::PAID]);

    expect($model->isPaid())->toBeTrue();
    expect($model->isCart())->toBeFalse();
});

it('checks if it is shipped', function () {
    $model = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::SHIPPED]);

    expect($model->isShipped())->toBeTrue();
    expect($model->isCart())->toBeFalse();
});

it('checks if it is delivered', function () {
    $model = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::DELIVERED]);

    expect($model->isDelivered())->toBeTrue();
    expect($model->isCart())->toBeFalse();
});

it('checks if it is completed', function () {
    $delivered = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::DELIVERED]);
    $canceled = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::CANCELED]);
    $paid = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::PAID]);

    expect($delivered->isCompleted())->toBeTrue();
    expect($canceled->isCompleted())->toBeTrue();
    expect($paid->isCompleted())->toBeFalse();
});

it('checks if it is pending', function () {
    $model = TestOrderModel::create(['name' => 'test', 'status' => OrderStatus::PENDING]);

    expect($model->isPending())->toBeTrue();
    expect($model->isCart())->toBeFalse();
});

it('has a cart scope', function () {
    TestOrderModel::create(['name' => 'cart-1', 'status' => OrderStatus::CART]);
    TestOrderModel::create(['name' => 'cart-2', 'status' => OrderStatus::CART]);
    TestOrderModel::create(['name' => 'paid-1', 'status' => OrderStatus::PAID]);

    $models = TestOrderModel::cart()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['cart-1', 'cart-2']);
});

it('has a checkout scope', function () {
    TestOrderModel::create(['name' => 'checkout-1', 'status' => OrderStatus::CHECKOUT]);
    TestOrderModel::create(['name' => 'checkout-2', 'status' => OrderStatus::CHECKOUT]);
    TestOrderModel::create(['name' => 'paid-1', 'status' => OrderStatus::PAID]);

    $models = TestOrderModel::checkout()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['checkout-1', 'checkout-2']);
});

it('has a paid scope', function () {
    TestOrderModel::create(['name' => 'paid-1', 'status' => OrderStatus::PAID]);
    TestOrderModel::create(['name' => 'paid-2', 'status' => OrderStatus::PAID]);
    TestOrderModel::create(['name' => 'cart-1', 'status' => OrderStatus::CART]);

    $models = TestOrderModel::paid()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['paid-1', 'paid-2']);
});

it('has a shipped scope', function () {
    TestOrderModel::create(['name' => 'shipped-1', 'status' => OrderStatus::SHIPPED]);
    TestOrderModel::create(['name' => 'shipped-2', 'status' => OrderStatus::SHIPPED]);
    TestOrderModel::create(['name' => 'cart-1', 'status' => OrderStatus::CART]);

    $models = TestOrderModel::shipped()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['shipped-1', 'shipped-2']);
});

it('has a delivered scope', function () {
    TestOrderModel::create(['name' => 'delivered-1', 'status' => OrderStatus::DELIVERED]);
    TestOrderModel::create(['name' => 'delivered-2', 'status' => OrderStatus::DELIVERED]);
    TestOrderModel::create(['name' => 'cart-1', 'status' => OrderStatus::CART]);

    $models = TestOrderModel::delivered()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['delivered-1', 'delivered-2']);
});

it('has a pending scope', function () {
    TestOrderModel::create(['name' => 'cart-1', 'status' => OrderStatus::CART]);
    TestOrderModel::create(['name' => 'checkout-1', 'status' => OrderStatus::CHECKOUT]);
    TestOrderModel::create(['name' => 'paid-1', 'status' => OrderStatus::PAID]);

    $models = TestOrderModel::pending()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['cart-1', 'checkout-1']);
});

it('has a processing scope', function () {
    TestOrderModel::create(['name' => 'paid-1', 'status' => OrderStatus::PAID]);
    TestOrderModel::create(['name' => 'shipped-1', 'status' => OrderStatus::SHIPPED]);
    TestOrderModel::create(['name' => 'cart-1', 'status' => OrderStatus::CART]);

    $models = TestOrderModel::processing()->get();

    expect($models)->toHaveCount(2)
        ->and($models->pluck('name')->all())->toBe(['paid-1', 'shipped-1']);
});

it('has a whereStatus scope', function () {
    TestOrderModel::create(['name' => 'cart-1', 'status' => OrderStatus::CART]);
    TestOrderModel::create(['name' => 'paid-1', 'status' => OrderStatus::PAID]);

    $models = TestOrderModel::whereStatus(OrderStatus::PAID)->get();

    expect($models)->toHaveCount(1)
        ->and($models->first()->name)->toBe('paid-1');
});
