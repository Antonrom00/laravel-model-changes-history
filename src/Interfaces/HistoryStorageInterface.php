<?php

namespace Antonrom\ModelChangesHistory\Interfaces;

use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface HistoryStorageInterface
{
    /**
     * This function will record Change model to history storage
     *
     * @param Change $change
     */
    public function recordChange(Change $change): void;

    /**
     * This function will return all changes history using storage.
     * If the model is set - return all changes history for it.
     *
     * @param Model|null $model
     * @return Collection
     */
    public function getHistoryChanges(?Model $model = null): Collection;

    /**
     * This function will return the latest change using storage.
     * If the model is set - return the latest change for it.
     *
     * @param Model|null $model
     * @return Change|null
     */
    public function getLatestChange(?Model $model = null): ?Change;

    /**
     * This function will delete all changes history using storage.
     * If the model is set - clear all changes history for it.
     *
     * @param Model|null $model
     */
    public function deleteHistoryChanges(?Model $model = null): void;
}
