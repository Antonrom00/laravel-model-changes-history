<?php

namespace Antonrom\ModelChangesHistory\Stores;

use Antonrom\ModelChangesHistory\Exceptions\StorageForFoundException;
use Antonrom\ModelChangesHistory\Exceptions\StorageNotFoundException;
use Antonrom\ModelChangesHistory\Interfaces\HistoryStorageInterface;

class HistoryStorageRegistry
{
    const STORAGE_DATABASE = 'database';
    const STORAGE_REDIS = 'redis';
    const STORAGE_FILE = 'file';

    private $container = [];

    public static function create(): self
    {
        return (new self())
            ->add(self::STORAGE_DATABASE, new DatabaseHistoryStorage())
            ->add(self::STORAGE_REDIS, new RedisHistoryStorage())
            ->add(self::STORAGE_FILE, new FileHistoryStorage());
    }

    /**
     * @param string $name
     * @param HistoryStorageInterface $storage
     * @return void
     */
    public function add(string $name, HistoryStorageInterface $storage): self
    {
        $this->container[$name] = $storage;

        return $this;
    }

    /**
     * @param string $name
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
