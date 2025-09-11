<?php

namespace Roberts\Support\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUpdater
{
    protected static function bootHasUpdater()
    {
        static::saving(function ($model) {
            if (Auth::check()) {
                $model->updater_id = Auth::id();
            }
        });
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updater_id');
    }
}