<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\OrderStatus;

trait HasOrderStatus
{
    public function initializeHasOrderStatus(): void
    {
        $this->casts['status'] = OrderStatus::class;
    }

    protected static function bootHasOrderStatus()
    {
        static::creating(function ($model) {
            $model->status = OrderStatus::CART;
        });
    }

    public function getStatus(): OrderStatus
    {
        return $this->status ?? OrderStatus::CART;
    }

    public function setStatus(OrderStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function moveToCheckout(): static
    {
        return $this->setStatus(OrderStatus::CHECKOUT);
    }

    public function markAsPaid(): static
    {
        return $this->setStatus(OrderStatus::PAID);
    }

    public function markAsShipped(): static
    {
        return $this->setStatus(OrderStatus::SHIPPED);
    }

    public function markAsDelivered(): static
    {
        return $this->setStatus(OrderStatus::DELIVERED);
    }

    public function isCart(): bool
    {
        return $this->getStatus()->isCart();
    }

    public function isCheckout(): bool
    {
        return $this->getStatus()->isCheckout();
    }

    public function isPaid(): bool
    {
        return $this->getStatus()->isPaid();
    }

    public function isShipped(): bool
    {
        return $this->getStatus()->isShipped();
    }

    public function isDelivered(): bool
    {
        return $this->getStatus()->isDelivered();
    }

    public function isCompleted(): bool
    {
        return $this->getStatus()->isCompleted();
    }

    public function isPending(): bool
    {
        return $this->getStatus()->isPending();
    }

    public function scopeCart(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::CART);
    }

    public function scopeCheckout(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::CHECKOUT);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::PAID);
    }

    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::SHIPPED);
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::DELIVERED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [OrderStatus::CART, OrderStatus::CHECKOUT]);
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->whereIn('status', [OrderStatus::PAID, OrderStatus::SHIPPED]);
    }

    public function scopeWhereStatus(Builder $query, OrderStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
