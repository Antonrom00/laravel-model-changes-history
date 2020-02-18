<?php

namespace Antonrom\ModelChangesHistory\Stores;

use Antonrom\ModelChangesHistory\Interfaces\HistoryStorageInterface;
use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class RedisHistoryStorage implements HistoryStorageInterface
{
    /**
     * @var \Illuminate\Redis\Connections\Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $key;

    public function __construct()
    {
        $this->connection = Redis::connection(config('model_changes_history.stores.redis.connection', 'model_changes_history'));
        $this->key = config('model_changes_history.stores.redis.key', 'model_changes_history');
    }

    public function recordChange(Change $change): void
    {
        $this->connection->zadd($this->key, [$change->toJson() => now()->timestamp]);
    }

    public function getHistoryChanges(?Model $model = null): Collection
    {
        return $model ? $this->getModelHistoryChanges($model) : $this->getAllChanges();
    }

    public function getLatestChange(?Model $model = null): ?Change
    {
        return $this->getHistoryChanges($model)->last();
    }

    public function deleteHistoryChanges(?Model $model = null): void
    {
        $model
            ? $this->deleteModelHistoryChanges($model)
            : $this->connection->zremrangebyrank($this->key, 0, -1);
    }

    protected function getAllChanges(): Collection
    {
        $changes = $this->connection->zrange($this->key, 0, -1);

        $historyChanges = collect();
        foreach ($changes as $change) {
            $historyChanges->add(Change::make(json_decode($change, true)));
        }

        return $historyChanges;
    }

    protected function getModelHistoryChanges(Model $model): Collection
    {
        $historyChanges = $this->getAllChanges();

        return $historyChanges->where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->values();
    }

    protected function deleteModelHistoryChanges(Model $model): void
    {
        $historyChanges = $this->getModelHistoryChanges($model);

        foreach ($historyChanges as $change) {
            $this->connection->zrem($this->key, $change->toJson());
        }
    }
}
