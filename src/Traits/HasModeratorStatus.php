<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\ModeratorStatus;

trait HasModeratorStatus
{
    public function initializeHasModeratorStatus(): void
    {
        $this->casts['status'] = ModeratorStatus::class;
    }

    protected static function bootHasModeratorStatus()
    {
        static::creating(function ($model) {
            if (! isset($model->attributes['status']) && $model->status === null) {
                $model->status = ModeratorStatus::PENDING;
            }
        });
    }

    public function getStatus(): ModeratorStatus
    {
        return $this->status ?? ModeratorStatus::PENDING;
    }

    public function setStatus(ModeratorStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function markAsPending(): static
    {
        return $this->setStatus(ModeratorStatus::PENDING);
    }

    public function flag(): static
    {
        return $this->setStatus(ModeratorStatus::FLAGGED);
    }

    public function approve(): static
    {
        return $this->setStatus(ModeratorStatus::APPROVED);
    }

    public function reject(): static
    {
        return $this->setStatus(ModeratorStatus::REJECTED);
    }

    public function isPending(): bool
    {
        return $this->getStatus()->isPending();
    }

    public function isFlagged(): bool
    {
        return $this->getStatus()->isFlagged();
    }

    public function isApproved(): bool
    {
        return $this->getStatus()->isApproved();
    }

    public function isRejected(): bool
    {
        return $this->getStatus()->isRejected();
    }

    public function isDecided(): bool
    {
        return $this->getStatus()->isDecided();
    }

    public function needsModeration(): bool
    {
        return $this->getStatus()->needsModeration();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ModeratorStatus::PENDING);
    }

    public function scopeFlagged(Builder $query): Builder
    {
        return $query->where('status', ModeratorStatus::FLAGGED);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ModeratorStatus::APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ModeratorStatus::REJECTED);
    }

    public function scopeDecided(Builder $query): Builder
    {
        return $query->whereIn('status', [ModeratorStatus::APPROVED, ModeratorStatus::REJECTED]);
    }

    public function scopeNeedingModeration(Builder $query): Builder
    {
        return $query->whereIn('status', [ModeratorStatus::PENDING, ModeratorStatus::FLAGGED]);
    }

    public function scopeWhereStatus(Builder $query, ModeratorStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
