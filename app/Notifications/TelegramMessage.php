<?php

namespace App\Notifications;

class TelegramMessage
{
    public function __construct(public string $text) {}
}
