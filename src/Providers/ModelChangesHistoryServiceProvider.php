<?php

namespace Antonrom\ModelChangesHistory\Providers;

use Illuminate\Support\ServiceProvider;

class ModelChangesHistoryServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/model_changes_history.php' => config_path('package.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Provide the change recorder via ioc.
     *
     * @return array
     */
    public function provides()
    {
        return [
            //
        ];
    }

    /**
     * Register the provided classes.
     */
    public function register()
    {
        //
    }
}
