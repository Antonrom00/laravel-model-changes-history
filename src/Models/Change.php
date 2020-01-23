<?php

namespace Antonrom\ModelChangesHistory\Models;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Change extends Model
{
    const TYPE_CREATED = 'created';
    const TYPE_UPDATED = 'updated';
    const TYPE_DELETED = 'deleted';
    const TYPE_RESTORED = 'restored';
    const TYPE_FORCE_DELETED = 'forceDeleted';

    public $timestamps = false;

    protected $fillable = [
        'model_type',
        'model_id',

        'before_changes',
        'after_changes',
        'changes',
        'change_type',

        'changer_type',
        'changer_id',

        'stack_trace',

        'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function getTable(): string
    {
        return config('model_changes_history.stores.database.table');
    }


    /*
     * Scopes
     */

    public function scopeWhereModel(Builder $query, Model $model): Builder
    {
        return $query->whereModelType(get_class($model))->whereModelId($model->id);
    }

    public function scopeWhereChanger(Builder $query, Authenticatable $changer): Builder
    {
        return $query->whereChangerType(get_class($changer))->whereChangerId($changer->id);
    }

    public function scopeWhereCreatedBetween(Builder $query, CarbonInterface $from, CarbonInterface $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeWhereType(Builder $query, string $type): Builder
    {
        return $query->whereChangeType($type);
    }


    /*
     * Relations
     */

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function changer(): HasOne
    {
        return $this->hasOne($this->changer_type, 'id', 'changer_id');
    }
}
