<?php


namespace App\Traits;

use App\Notifications\Telegram;
use Illuminate\Support\Facades\Notification;

trait TraitTelegram
{
    public function sendTelegramMessage($chatId, $message)
    {
        Notification::send($chatId, new Telegram($message));
    }
}
