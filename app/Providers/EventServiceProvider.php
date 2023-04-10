<?php

namespace App\Providers;

use App\Listeners\ConnectionLog;
use App\Listeners\DatabaseQueryLog;
use Illuminate\Database\Events\ConnectionEvent;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ConnectionEvent::class => [
            ConnectionLog::class,
        ],
        QueryExecuted::class => [
            DatabaseQueryLog::class,
        ],
    ];

    public function boot()
    {
    }
}
