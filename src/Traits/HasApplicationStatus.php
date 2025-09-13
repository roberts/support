<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\ApplicationStatus;

trait HasApplicationStatus
{
    public function initializeHasApplicationStatus(): void
    {
        $this->casts['status'] = ApplicationStatus::class;
    }

    protected static function bootHasApplicationStatus()
    {
        static::creating(function ($model) {
            if (!isset($model->attributes['status']) && $model->status === null) {
                $model->status = ApplicationStatus::STARTED;
            }
        });
    }

    public function getStatus(): ApplicationStatus
    {
        return $this->status ?? ApplicationStatus::STARTED;
    }

    public function setStatus(ApplicationStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function start(): static
    {
        return $this->setStatus(ApplicationStatus::STARTED);
    }

    public function verify(): static
    {
        return $this->setStatus(ApplicationStatus::VERIFIED);
    }

    public function apply(): static
    {
        return $this->setStatus(ApplicationStatus::APPLIED);
    }

    public function accept(): static
    {
        return $this->setStatus(ApplicationStatus::ACCEPTED);
    }

    public function reject(): static
    {
        return $this->setStatus(ApplicationStatus::REJECTED);
    }

    public function isStarted(): bool
    {
        return $this->getStatus()->isStarted();
    }

    public function isVerified(): bool
    {
        return $this->getStatus()->isVerified();
    }

    public function isApplied(): bool
    {
        return $this->getStatus()->isApplied();
    }

    public function isAccepted(): bool
    {
        return $this->getStatus()->isAccepted();
    }

    public function isRejected(): bool
    {
        return $this->getStatus()->isRejected();
    }

    public function isPending(): bool
    {
        return $this->getStatus()->isPending();
    }

    public function isDecided(): bool
    {
        return $this->getStatus()->isDecided();
    }

    public function scopeStarted(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::STARTED);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::VERIFIED);
    }

    public function scopeApplied(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::APPLIED);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::ACCEPTED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::REJECTED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [ApplicationStatus::STARTED, ApplicationStatus::VERIFIED, ApplicationStatus::APPLIED]);
    }

    public function scopeDecided(Builder $query): Builder
    {
        return $query->whereIn('status', [ApplicationStatus::ACCEPTED, ApplicationStatus::REJECTED]);
    }

    public function scopeWhereStatus(Builder $query, ApplicationStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
