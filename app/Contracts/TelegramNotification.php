<?php

namespace App\Contracts;

use App\Notifications\TelegramMessage;

interface TelegramNotification
{
    public function toTelegram(object $notifiable): TelegramMessage;
}
