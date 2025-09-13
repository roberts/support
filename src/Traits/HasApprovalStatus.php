<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\ApprovalStatus;

trait HasApprovalStatus
{
    public function initializeHasApprovalStatus(): void
    {
        $this->casts['status'] = ApprovalStatus::class;
    }

    protected static function bootHasApprovalStatus()
    {
        static::creating(function ($model) {
            if (! isset($model->attributes['status']) && $model->status === null) {
                $model->status = ApprovalStatus::PENDING;
            }
        });
    }

    public function getStatus(): ApprovalStatus
    {
        return $this->status ?? ApprovalStatus::SUBMITTED;
    }

    public function setStatus(ApprovalStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function submit(): static
    {
        return $this->setStatus(ApprovalStatus::SUBMITTED);
    }

    public function approve(): static
    {
        return $this->setStatus(ApprovalStatus::APPROVED);
    }

    public function reject(): static
    {
        return $this->setStatus(ApprovalStatus::REJECTED);
    }

    public function isSubmitted(): bool
    {
        return $this->getStatus()->isSubmitted();
    }

    public function isApproved(): bool
    {
        return $this->getStatus()->isApproved();
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

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', ApprovalStatus::SUBMITTED);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ApprovalStatus::APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ApprovalStatus::REJECTED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ApprovalStatus::SUBMITTED);
    }

    public function scopeDecided(Builder $query): Builder
    {
        return $query->whereIn('status', [ApprovalStatus::APPROVED, ApprovalStatus::REJECTED]);
    }

    public function scopeWhereStatus(Builder $query, ApprovalStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
