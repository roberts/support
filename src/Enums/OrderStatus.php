<?php

namespace Roberts\Support\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CART = 'cart';
    case CHECKOUT = 'checkout';
    case PAID = 'paid';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CART => 'In Cart',
            self::CHECKOUT => 'Checkout',
            self::PAID => 'Paid',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELED => 'Canceled',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isCart(): bool
    {
        return $this === self::CART;
    }

    public function isCheckout(): bool
    {
        return $this === self::CHECKOUT;
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isShipped(): bool
    {
        return $this === self::SHIPPED;
    }

    public function isDelivered(): bool
    {
        return $this === self::DELIVERED;
    }

    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::DELIVERED, self::CANCELED]);
    }
}
