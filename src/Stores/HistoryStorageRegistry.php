<?php

namespace Antonrom\ModelChangesHistory\Stores;

use Antonrom\ModelChangesHistory\Exceptions\StorageNotFoundException;
use Antonrom\ModelChangesHistory\Interfaces\HistoryStorageInterface;

class HistoryStorageRegistry
{
    public const STORAGE_DATABASE = 'database';
    public const STORAGE_REDIS = 'redis';
    public const STORAGE_FILE = 'file';

    private $storagesMap = [
        self::STORAGE_DATABASE => DatabaseHistoryStorage::class,
        self::STORAGE_REDIS => RedisHistoryStorage::class,
        self::STORAGE_FILE => FileHistoryStorage::class,
    ];

    private $container = [];

    /**
     * Create the instance of the class with default history stores
     *
     * @return static
     */
    public static function create(): self
    {
        return (new self());
    }

    /**
     * Get the instance of the class history storage from container
     *
     * @param string $name
     *
     * @return HistoryStorageInterface
     * @throws StorageNotFoundException
     */
    public function get(string $name): HistoryStorageInterface
    {
        if (!isset($this->storagesMap[$name])) {
            throw new StorageNotFoundException;
        }

        if (!isset($this->container[$name])) {
            $this->container[$name] = new $this->storagesMap[$name];
        }

        return $this->container[$name];
    }
}