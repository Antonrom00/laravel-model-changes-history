<?php

namespace Antonrom\ModelHistory\Interfaces;

interface ModelHasHistoryInterface
{
    public function showHistory();

    public function clearHistory();
}
