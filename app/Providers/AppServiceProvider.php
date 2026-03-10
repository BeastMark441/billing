<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
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
        // Event listeners are auto-discovered
        \App\Models\Order::observe(\App\Observers\UserLogObserver::class);
        \App\Models\Ticket::observe(\App\Observers\UserLogObserver::class);
        \App\Models\TicketMessage::observe(\App\Observers\UserLogObserver::class);
    }
}
