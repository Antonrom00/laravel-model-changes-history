<?php

namespace Antonrom\ModelChangesHistory\Tests;

use Antonrom\ModelChangesHistory\Providers\ModelChangesHistoryServiceProvider;
use CreateModelChangesHistoryTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase();
    }

    /**
     * Get the package service providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            ModelChangesHistoryServiceProvider::class,
        ];
    }

    /**
     * Get the package service aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
            'ChangesHistory' => 'Antonrom\ModelChangesHistory\Facade\HistoryChanges',
            'Storages' => 'Antonrom\ModelChangesHistory\Facade\HistoryStorage',
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('app.debug', true);
        $app['config']->set('model_changes_history.storage', 'database');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setupDatabase(): void
    {
        include_once __DIR__ . '/../publishable/database/migrations/create_model_changes_history_table.php';

        (new CreateModelChangesHistoryTable())->up();

        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('body')->nullable();
            $table->string('password')->nullable();
            $table->softDeletes();
        });
    }
}