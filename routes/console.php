<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Проверка просроченных заказов и автопродление (каждый день)
Schedule::command('billing:check-expirations')->dailyAt('00:01');

// Синхронизация статусов серверов в Pterodactyl (каждый час)
Schedule::command('billing:sync-status')->hourly();

// Синхронизация платежей T-Bank, которые зависли в статусе ожидания (каждые 15 минут)
Schedule::command('payments:sync-tbank')->everyFifteenMinutes();
