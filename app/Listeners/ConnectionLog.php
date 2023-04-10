<?php

namespace App\Listeners;

use Illuminate\Database\Events\ConnectionEvent;
use Illuminate\Support\Facades\Log;

class ConnectionLog
{
    public function handle(ConnectionEvent $event): void
    {
        Log::debug("Connection '{$event->connectionName}' has transaction event", [
            'type' => get_class($event),
        ]);
    }
}
