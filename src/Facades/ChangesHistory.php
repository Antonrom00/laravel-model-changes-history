<?php

namespace Antonrom\ModelChangesHistory\Facades;

use Illuminate\Support\Facades\Facade;

class ChangesHistory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'changesHistory';
    }
}
