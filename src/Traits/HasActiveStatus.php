<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\ActiveStatus;

trait HasActiveStatus
{
    public function initializeHasActiveStatus(): void
    {
        $this->casts['status'] = ActiveStatus::class;
    }

    protected static function bootHasActiveStatus()
    {
        static::creating(function ($model) {
            if (!isset($model->attributes['status']) && $model->status === null) {
                $model->status = ActiveStatus::ACTIVE;
            }
        });
    }

    public function getStatus(): ActiveStatus
    {
        return $this->status ?? ActiveStatus::INACTIVE;
    }

    public function setStatus(ActiveStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function activate(): static
    {
        return $this->setStatus(ActiveStatus::ACTIVE);
    }

    public function deactivate(): static
    {
        return $this->setStatus(ActiveStatus::INACTIVE);
    }

    public function isActive(): bool
    {
        return $this->getStatus()->isActive();
    }

    public function isInactive(): bool
    {
        return $this->getStatus()->isInactive();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ActiveStatus::ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', ActiveStatus::INACTIVE);
    }

    public function scopeWhereStatus(Builder $query, ActiveStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
