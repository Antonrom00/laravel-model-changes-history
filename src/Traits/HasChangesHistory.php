<?php

namespace Antonrom\ModelChangesHistory\Traits;

use Antonrom\ModelChangesHistory\Facades\HistoryStorage;
use Antonrom\ModelChangesHistory\Models\Change;
use Antonrom\ModelChangesHistory\Observers\ModelChangesHistoryObserver;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

trait HasChangesHistory
{
    public static function boot(): void
    {
        parent::boot();

        self::observe(ModelChangesHistoryObserver::class);
    }

    public function latestChange(): ?Change
    {
        return HistoryStorage::getLatestChange($this);
    }

    public function latestChangeMorph(): MorphOne
    {
        return $this->morphOne(Change::class, 'model')->latest();
    }

    public function historyChanges(): Collection
    {
        return HistoryStorage::getHistoryChanges($this);
    }

    public function historyChangesMorph(): MorphMany
    {
        return $this->morphMany(Change::class, 'model')->latest();
    }

    public function clearHistoryChanges(): void
    {
        HistoryStorage::deleteHistoryChanges($this);
    }
}
