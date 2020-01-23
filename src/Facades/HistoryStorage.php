<?php

namespace Antonrom\ModelChangesHistory\Facades;

use Illuminate\Support\Facades\Facade;

class HistoryStorage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'historyStorage';
    }
}
