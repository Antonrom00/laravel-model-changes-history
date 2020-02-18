<?php

namespace Antonrom\ModelChangesHistory\Stores;

use Antonrom\ModelChangesHistory\Interfaces\HistoryStorageInterface;
use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class FileHistoryStorage implements HistoryStorageInterface
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $storage;

    /**
     * @var string
     */
    protected $fileName;

    public function __construct()
    {
        $this->storage = Storage::disk(config('model_changes_history.stores.file.disk', 'model_changes_history'));
        $this->fileName = config('model_changes_history.stores.file.file_name', 'changes_history.txt');
    }

    public function recordChange(Change $change): void
    {
        $this->storage->append($this->fileName, $change->toJson());
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
        $model ? $this->deleteModelHistoryChanges($model) : $this->storage->delete($this->fileName);
    }

    protected function getAllChanges(): Collection
    {
        try {
            $fileContent = $this->storage->get($this->fileName);
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

    protected function getModelHistoryChanges(Model $model): Collection
    {
        $historyChanges = $this->getAllChanges();

        return $historyChanges->where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->values();
    }

    protected function deleteModelHistoryChanges(Model $model): void
    {
        $historyChanges = $this->getAllChanges();
        $this->storage->delete($this->fileName);

        $newHistoryChanges = $historyChanges->where('model_type', get_class($model))
            ->where('model_id', '!=', $model->id)
            ->values();

        foreach ($newHistoryChanges as $change) {
            $this->storage->append($this->fileName, $change->toJson());
        }
    }
}
