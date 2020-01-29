<?php

namespace Antonrom\ModelChangesHistory\Stores;

use Antonrom\ModelChangesHistory\Exceptions\StorageNotFoundException;
use Antonrom\ModelChangesHistory\Interfaces\HistoryStorageInterface;

class HistoryStorageRegistry
{
    const STORAGE_DATABASE = 'database';
    const STORAGE_REDIS = 'redis';
    const STORAGE_FILE = 'file';

    private $container = [];

    /**
     * Create the instance of the class with default history stores
     *
     * @return static
     */
    public static function create(): self
    {
        return (new self())
            ->add(self::STORAGE_DATABASE, new DatabaseHistoryStorage())
            ->add(self::STORAGE_REDIS, new RedisHistoryStorage())
            ->add(self::STORAGE_FILE, new FileHistoryStorage());
    }

    /**
     * Add the new history storage to container
     *
     * @param string $name
     * @param HistoryStorageInterface $storage
     *
     * @return HistoryStorageRegistry
     */
    public function add(string $name, HistoryStorageInterface $storage): self
    {
        $this->container[$name] = $storage;

        return $this;
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
        if (!isset($this->container[$name])) {
            throw new StorageNotFoundException;
        }

        return $this->container[$name];
    }
}
