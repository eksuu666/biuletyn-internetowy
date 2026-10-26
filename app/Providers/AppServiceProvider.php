<?php

namespace App\Providers;

use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(QueueBusy::class, static function ($event) {
            Log::warning('Przekroczony próg kolejki', ['connection' => $event->connection, 'queue' => $event->queue, 'size' => $event->size]);
        });
    }
}
