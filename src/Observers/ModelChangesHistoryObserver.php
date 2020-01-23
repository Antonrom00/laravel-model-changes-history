<?php

namespace Antonrom\ModelChangesHistory\Observers;

use Antonrom\ModelChangesHistory\Facades\ChangesHistory;
use Antonrom\ModelChangesHistory\Facades\HistoryStorage;
use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModelChangesHistoryObserver
{
    protected $ignoredActions;

    public function __construct()
    {
        $this->ignoredActions = config('model_changes_history.ignored_actions');
    }

    public function created(Model $model)
    {
        if (!in_array(Change::TYPE_CREATED, $this->ignoredActions)) {
            HistoryStorage::recordChange(ChangesHistory::createChange(Change::TYPE_CREATED, $model, Auth::user()));
        }
    }

    public function updated(Model $model)
    {
        if (!in_array(Change::TYPE_UPDATED, $this->ignoredActions)) {
            HistoryStorage::recordChange(ChangesHistory::createChange(Change::TYPE_UPDATED, $model, Auth::user()));
        }
    }

    public function deleted(Model $model)
    {
        if (!in_array(Change::TYPE_DELETED, $this->ignoredActions)) {
            HistoryStorage::recordChange(ChangesHistory::createChange(Change::TYPE_DELETED, $model, Auth::user()));
        }
    }

    public function restored(Model $model)
    {
        if (!in_array(Change::TYPE_RESTORED, $this->ignoredActions)) {
            HistoryStorage::recordChange(ChangesHistory::createChange(Change::TYPE_RESTORED, $model, Auth::user()));
        }
    }

    public function forceDeleted(Model $model)
    {
        if (!in_array(Change::TYPE_FORCE_DELETED, $this->ignoredActions)) {
            HistoryStorage::recordChange(ChangesHistory::createChange(Change::TYPE_FORCE_DELETED, $model, Auth::user()));
        }
    }
}
