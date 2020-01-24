<?php

namespace Antonrom\ModelChangesHistory\Services;

use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class HistoryStorageService
{
    /**
     * @var bool
     */
    protected $recordHistoryChanges;

    /**
     * @var string
     */
    protected $defaultDriver;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var \Illuminate\Redis\Connections\Connection
     */
    protected $redis;

    /**
     * @var string
     */
    protected $redisKey;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $fileStorage;

    /**
     * @var string
     */
    protected $fileName;

    public function __construct()
    {
        $this->recordHistoryChanges = config('model_changes_history.record_changes_history')
            ? (config('model_changes_history.use_only_for_debug') ? config('app.debug') : true)
            : false;

        $this->defaultDriver = config('model_changes_history.default_driver');

        switch ($this->defaultDriver) {
            case 'database':
                $this->tableName = config('model_changes_history.stores.database.table');

                break;
            case 'redis':
                $this->redis = Redis::connection(config('model_changes_history.stores.redis.connection'));
                $this->redisKey = config('model_changes_history.stores.redis.key');

                break;
            case 'file' :
                $this->fileStorage = Storage::disk(config('model_changes_history.stores.file.disk'));
                $this->fileName = config('model_changes_history.stores.file.file_name');

                break;
            default:
                break;
        }
    }

    public function recordChange(Change $change): void
    {
        if (!$this->recordHistoryChanges) return;

        switch ($this->defaultDriver) {
            case 'database':
                $change->save();
                return;
            case 'redis':
                $this->redis->zadd($this->redisKey, [$change->toJson() => now()->timestamp]);
                return;
            case 'file':
                $this->fileStorage->append($this->fileName, $change->toJson());
                return;
            default:
                return;
        }
    }

    public function getHistoryChanges(?Model $model = null): Collection
    {
        switch ($this->defaultDriver) {
            case 'database':
                return $model
                    ? $model->historyChangesMorph
                    : Change::latest()->get();
            case 'redis':
                return $model
                    ? $this->getHistoryChangesFromRedis($model)
                    : $this->getAllChangesFromRedis();
            case 'file':
                return $model
                    ? $this->getHistoryChangesFromFile($model)
                    : $this->getAllChangesFromFile();
            default:
                return collect();
        }
    }

    public function getLatestChange(?Model $model = null): ?Change
    {
        return $this->getHistoryChanges($model)->last();
    }

    public function deleteHistoryChanges(?Model $model = null): void
    {
        switch ($this->defaultDriver) {
            case 'database':
                $model
                    ? $model->historyChangesMorph()->delete()
                    : DB::table($this->tableName)->truncate();

                return;
            case 'redis':
                $model
                    ? $this->deleteHistoryChangesFromRedis($model)
                    : $this->redis->zremrangebyrank($this->redisKey, 0, -1);

                return;
            case 'file':
                $model
                    ? $this->deleteHistoryChangesFromFile($model)
                    : $this->fileStorage->delete($this->fileName);

                return;
            default:
                return;
        }
    }

    protected function getAllChangesFromRedis(): Collection
    {
        $changes = $this->redis->zrange($this->redisKey, 0, -1);

        $historyChanges = collect();
        foreach ($changes as $change) {
            $historyChanges->add(Change::make(json_decode($change, true)));
        }

        return $historyChanges;
    }

    protected function getHistoryChangesFromRedis(Model $model): Collection
    {
        $historyChanges = $this->getAllChangesFromRedis();

        return $historyChanges->where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->values();
    }

    protected function deleteHistoryChangesFromRedis(Model $model): void
    {
        $historyChanges = $this->getHistoryChangesFromRedis($model);

        foreach ($historyChanges as $change) {
            $this->redis->zrem($this->redisKey, $change->toJson());
        }
    }

    protected function getAllChangesFromFile(): Collection
    {
        try {
            $fileContent = $this->fileStorage->get($this->fileName);
        } catch (FileNotFoundException $e) {
            return collect();
        }

        $historyChanges = collect();

        $fileLines = explode("\n", $fileContent);
        foreach ($fileLines as $change) {
            $historyChanges->add(Change::make(json_decode($change, true)));
        }

        return $historyChanges;
    }

    protected function getHistoryChangesFromFile(Model $model): Collection
    {
        $historyChanges = $this->getAllChangesFromFile();

        return $historyChanges->where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->values();
    }

    protected function deleteHistoryChangesFromFile(Model $model): void
    {
        $historyChanges = $this->getAllChangesFromFile();
        $this->fileStorage->delete($this->fileName);

        $newHistoryChanges = $historyChanges->where('model_type', get_class($model))
            ->where('model_id', '!=', $model->id)
            ->values();

        foreach ($newHistoryChanges as $change) {
            $this->fileStorage->append($this->fileName, $change->toJson());
        }
    }
}
