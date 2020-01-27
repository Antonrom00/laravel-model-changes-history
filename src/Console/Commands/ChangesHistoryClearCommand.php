<?php

namespace Antonrom\ModelChangesHistory\Console\Commands;

use Antonrom\ModelChangesHistory\Facades\HistoryStorage;
use Illuminate\Console\Command;

class ChangesHistoryClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'changes-history:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear changes history for all models.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        HistoryStorage::deleteHistoryChanges();
    }
}
