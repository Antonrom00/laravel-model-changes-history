<?php

namespace Antonrom\ModelChangesHistory\Stores;

use Antonrom\ModelChangesHistory\Interfaces\HistoryStorageInterface;
use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseHistoryStorage implements HistoryStorageInterface
{
    /**
     * @var string
     */
    protected $tableName;

    public function __construct()
    {
        $this->tableName = config('model_changes_history.stores.database.table', 'model_changes_history');
    }

    public function recordChange(Change $change): void
    {
        $change->save();
    }

    public function getHistoryChanges(?Model $model = null): Collection
    {
        return $model ? $model->historyChangesMorph : Change::latest()->get();
    }

    public function getLatestChange(?Model $model = null): ?Change
    {
        return $model ? $model->latestChangeMorph : $this->getHistoryChanges()->last();
    }

    public function deleteHistoryChanges(?Model $model = null): void
    {
        $model ? $model->historyChangesMorph()->delete() : DB::table($this->tableName)->truncate();
    }
}
