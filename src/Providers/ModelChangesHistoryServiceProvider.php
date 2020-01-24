<?php

namespace Antonrom\ModelChangesHistory\Providers;

use Antonrom\ModelChangesHistory\Services\HistoryStorageService;
use Antonrom\ModelChangesHistory\Services\ChangesHistoryService;
use Illuminate\Support\ServiceProvider;

class ModelChangesHistoryServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $configDir = [
            __DIR__ . '/../../publishable/config/model_changes_history.php' =>
                config_path('model_changes_history.php'),
        ];

        $timestamp = date('Y_m_d_His', time());
        $tableName = config('model_changes_history.stores.database.table');

        $migrationDir = [
            __DIR__ . '/../../publishable/database/migrations/create_model_changes_history_table.php' =>
                database_path("/migrations/{$timestamp}_create_{$tableName}_table.php"),
        ];

        $this->publishes($configDir, 'config');
        $this->publishes($migrationDir, 'migrations');
        $this->publishes(array_merge($configDir, $migrationDir), 'model-changes-history');
    }

    /**
     * Register the provided classes.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../publishable/config/model_changes_history.php', 'model_changes_history'
        );

        config([
            'database.redis.model_changes_history' =>
                config('model_changes_history.stores.redis.model_changes_history'),

            'filesystems.disks.model_changes_history' =>
                config('model_changes_history.stores.file.model_changes_history'),
        ]);

        $this->app->bind('changesHistory', ChangesHistoryService::class);
        $this->app->bind('historyStorage', HistoryStorageService::class);
    }
}
