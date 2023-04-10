<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class DatabaseQueryLog
{
    public function handle(QueryExecuted $event): void
    {
        Log::debug($event->sql, [
            'bindings' => $event->bindings,
            'time' => $event->time,
        ]);
    }
}
