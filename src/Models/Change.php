<?php

namespace Antonrom\ModelChangesHistory\Models;

use Carbon\CarbonInterface;
use DateTime;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Change
 *
 * @package Antonrom\ModelChangesHistory\Models
 *
 * @property int id
 *
 * @property int model_id
 * @property string model_type
 *
 * @property string before_changes
 * @property string after_changes
 * @property string changes
 * @property string change_type
 *
 * @property string changer_type
 * @property int changer_id
 *
 * @property string stack_trace
 *
 * @property DateTime created_at
 */
class Change extends Model
{
    public const TYPE_CREATED       = 'created';
    public const TYPE_UPDATED       = 'updated';
    public const TYPE_DELETED       = 'deleted';
    public const TYPE_RESTORED      = 'restored';
    public const TYPE_FORCE_DELETED = 'forceDeleted';

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

    protected $casts = [self::CREATED_AT => 'datetime'];

    public static function getTypes(): array
    {
        return [
            Change::TYPE_CREATED,
            Change::TYPE_UPDATED,
            Change::TYPE_DELETED,
            Change::TYPE_RESTORED,
            Change::TYPE_FORCE_DELETED,
        ];
    }

    public function getTable(): string
    {
        return config('model_changes_history.stores.database.table', 'model_changes_history');
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
        return $query->whereBetween(self::CREATED_AT, [$from, $to]);
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
