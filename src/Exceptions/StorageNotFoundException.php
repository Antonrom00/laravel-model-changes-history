<?php

namespace Antonrom\ModelChangesHistory\Exceptions;

use InvalidArgumentException;

class StorageNotFoundException extends InvalidArgumentException
{
    protected $message = 'No current storage found or installed.';
}
