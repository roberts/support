<?php

namespace Roberts\Support\Enums;

enum SubscriptionStatus: string
{
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case CANCELED = 'canceled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::TRIAL => 'Trial',
            self::ACTIVE => 'Active',
            self::CANCELED => 'Canceled',
            self::EXPIRED => 'Expired',
        };
    }

    public function isTrial(): bool
    {
        return $this === self::TRIAL;
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isValid(): bool
    {
        return in_array($this, [self::TRIAL, self::ACTIVE]);
    }

    public function isInvalid(): bool
    {
        return in_array($this, [self::CANCELED, self::EXPIRED]);
    }
}
