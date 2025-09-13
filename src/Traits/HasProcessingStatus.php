<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\ProcessingStatus;

trait HasProcessingStatus
{
    public function initializeHasProcessingStatus(): void
    {
        $this->casts['status'] = ProcessingStatus::class;
    }

    protected static function bootHasProcessingStatus()
    {
        static::creating(function ($model) {
            if (!isset($model->attributes['status']) && $model->status === null) {
                $model->status = ProcessingStatus::PENDING;
            }
        });
    }

    public function getStatus(): ProcessingStatus
    {
        return $this->status ?? ProcessingStatus::PENDING;
    }

    public function setStatus(ProcessingStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function markAsPending(): static
    {
        return $this->setStatus(ProcessingStatus::PENDING);
    }

    public function markAsProcessing(): static
    {
        return $this->setStatus(ProcessingStatus::PROCESSING);
    }

    public function markAsCompleted(): static
    {
        return $this->setStatus(ProcessingStatus::COMPLETED);
    }

    public function markAsFailed(): static
    {
        return $this->setStatus(ProcessingStatus::FAILED);
    }

    public function isPending(): bool
    {
        return $this->getStatus()->isPending();
    }

    public function isProcessing(): bool
    {
        return $this->getStatus()->isProcessing();
    }

    public function isCompleted(): bool
    {
        return $this->getStatus()->isCompleted();
    }

    public function isFailed(): bool
    {
        return $this->getStatus()->isFailed();
    }

    public function isFinished(): bool
    {
        return $this->getStatus()->isFinished();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ProcessingStatus::PENDING);
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', ProcessingStatus::PROCESSING);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', ProcessingStatus::COMPLETED);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', ProcessingStatus::FAILED);
    }

    public function scopeFinished(Builder $query): Builder
    {
        return $query->whereIn('status', [ProcessingStatus::COMPLETED, ProcessingStatus::FAILED]);
    }

    public function scopeWhereStatus(Builder $query, ProcessingStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
