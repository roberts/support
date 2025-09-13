<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\SubscriptionStatus;

trait HasSubscriptionStatus
{
    public function initializeHasSubscriptionStatus(): void
    {
        $this->casts['status'] = SubscriptionStatus::class;
    }

    protected static function bootHasSubscriptionStatus()
    {
        static::creating(function ($model) {
            $model->status = SubscriptionStatus::TRIAL;
        });
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status ?? SubscriptionStatus::TRIAL;
    }

    public function setStatus(SubscriptionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function startTrial(): static
    {
        return $this->setStatus(SubscriptionStatus::TRIAL);
    }

    public function activate(): static
    {
        return $this->setStatus(SubscriptionStatus::ACTIVE);
    }

    public function cancel(): static
    {
        return $this->setStatus(SubscriptionStatus::CANCELED);
    }

    public function expire(): static
    {
        return $this->setStatus(SubscriptionStatus::EXPIRED);
    }

    public function isTrial(): bool
    {
        return $this->getStatus()->isTrial();
    }

    public function isActive(): bool
    {
        return $this->getStatus()->isActive();
    }

    public function isCanceled(): bool
    {
        return $this->getStatus()->isCanceled();
    }

    public function isExpired(): bool
    {
        return $this->getStatus()->isExpired();
    }

    public function isValid(): bool
    {
        return $this->getStatus()->isValid();
    }

    public function isInvalid(): bool
    {
        return $this->getStatus()->isInvalid();
    }

    public function scopeTrial(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::TRIAL);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::ACTIVE);
    }

    public function scopeCanceled(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::CANCELED);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::EXPIRED);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->whereIn('status', [SubscriptionStatus::TRIAL, SubscriptionStatus::ACTIVE]);
    }

    public function scopeInvalid(Builder $query): Builder
    {
        return $query->whereIn('status', [SubscriptionStatus::CANCELED, SubscriptionStatus::EXPIRED]);
    }

    public function scopeWhereStatus(Builder $query, SubscriptionStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
