<?php

namespace Antonrom\ModelChangesHistory\Exceptions;

class StorageNotFoundException extends \Exception
{
    protected $message = 'No current storage found or installed.';
}
