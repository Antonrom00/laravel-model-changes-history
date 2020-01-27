<?php

namespace Antonrom\ModelChangesHistory\Services;

use Antonrom\ModelChangesHistory\Exceptions\StorageNotFoundException;
use Antonrom\ModelChangesHistory\Interfaces\HistoryStorageInterface;
use Antonrom\ModelChangesHistory\Models\Change;
use Antonrom\ModelChangesHistory\Stores\HistoryStorageRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HistoryStorageService
{
    /**
     * @var bool
     */
    protected $recordHistoryChanges;

    /**
     * @var HistoryStorageInterface
     */
    protected $historyStorage;

    /**
     * HistoryStorageService constructor.
     *
     * @throws StorageNotFoundException
     */
    public function __construct()
    {
        $recordChangesOnlyForDebug = config('model_changes_history.use_only_for_debug')
            ? config('app.debug')
            : true;

        $this->recordHistoryChanges = config('model_changes_history.record_changes_history')
            ? $recordChangesOnlyForDebug
            : false;

        $this->historyStorage = HistoryStorageRegistry::create()->get(config('model_changes_history.storage'));
    }

    public function recordChange(Change $change): void
    {
        if ($this->recordHistoryChanges) {
            $this->historyStorage->recordChange($change);
        }
    }

    public function getHistoryChanges(?Model $model = null): Collection
    {
        return $this->historyStorage->getHistoryChanges($model);
    }

    public function getLatestChange(?Model $model = null): ?Change
    {
        return $this->historyStorage->getLatestChange($model);
    }

    public function deleteHistoryChanges(?Model $model = null): void
    {
        $this->historyStorage->deleteHistoryChanges($model);
    }
}
