<?php

namespace Roberts\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Roberts\Support\Enums\PublishingStatus;

trait HasPublishingStatus
{
    public function initializeHasPublishingStatus(): void
    {
        $this->casts['status'] = PublishingStatus::class;
    }

    protected static function bootHasPublishingStatus()
    {
        static::creating(function ($model) {
            if (!isset($model->attributes['status']) && $model->status === null) {
                $model->status = PublishingStatus::DRAFT;
            }
        });
    }

    public function getStatus(): PublishingStatus
    {
        return $this->status ?? PublishingStatus::DRAFT;
    }

    public function setStatus(PublishingStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function publish(): static
    {
        return $this->setStatus(PublishingStatus::PUBLISHED);
    }

    public function unpublish(): static
    {
        return $this->setStatus(PublishingStatus::DRAFT);
    }

    public function archive(): static
    {
        return $this->setStatus(PublishingStatus::ARCHIVED);
    }

    public function isDraft(): bool
    {
        return $this->getStatus()->isDraft();
    }

    public function isPublished(): bool
    {
        return $this->getStatus()->isPublished();
    }

    public function isArchived(): bool
    {
        return $this->getStatus()->isArchived();
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PublishingStatus::DRAFT);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishingStatus::PUBLISHED);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', PublishingStatus::ARCHIVED);
    }

    public function scopeWhereStatus(Builder $query, PublishingStatus $status): Builder
    {
        return $query->where('status', $status);
    }
}
