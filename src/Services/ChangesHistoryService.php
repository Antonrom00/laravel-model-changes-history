<?php

namespace Antonrom\ModelChangesHistory\Services;

use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ChangesHistoryService
{
    /**
     * @var bool
     */
    protected $recordStackTrace;

    public function __construct()
    {
        $this->recordStackTrace = config('model_changes_history.record_stack_trace');
    }

    public function createChange(string $type, Model $model, ?Authenticatable $changer = null): Change
    {
        return Change::make([
            'model_type' => $modelType = get_class($model),
            'model_id'   => $model->id,

            'before_changes' => $originalModel = $this->getOriginalModel($model),
            'after_changes'  => $model->fresh(),
            'changes'        => $this->getAttributesChanges($model, $originalModel),
            'change_type'    => $type,

            'changer_type' => $changer ? get_class($changer) : null,
            'changer_id'   => $changer->id ?? null,

            'stack_trace' => $this->getStackStace(),

            'created_at' => now(),
        ]);
    }

    protected function getOriginalModel(Model $model)
    {
        $originalModel = clone $model;
        foreach ($model->getAttributes() as $key => $afterValue) {
            $beforeValue = $model->getOriginal($key);
            $originalModel->$key = $beforeValue;
        }

        return $originalModel;
    }

    protected function getAttributesChanges(Model $model, ?Model $originalModel = null): Collection
    {
        $originalModel = $originalModel ? : $this->getOriginalModel($model);
        $attributesChanges = collect();

        foreach ($model->getChanges() as $key => $afterValue) {
            $attributesChanges->put($key, [
                'before' => $originalModel->$key,
                'after'  => $afterValue,
            ]);
        }

        return $attributesChanges;
    }

    protected function getStackStace(): ?Collection
    {
        return $this->recordStackTrace
            ? collect(debug_backtrace())
                ->where('class', 'Illuminate\Database\Eloquent\Model')
                ->whereIn('function', ['create', 'update', 'save', 'delete', 'restore', 'forceDelete'])
                ->values()
            : null;
    }
}
