<?php

namespace App\Plugins\Telegram\Commands;

use App\Models\User;
use App\Plugins\Telegram\Telegram;

class GetLatestUrl extends Telegram {
    public $command = '/getlatesturl';
    public $description = 'Bind Telegram account to website';

    public function handle($message, $match = []) {
        $telegramService = $this->telegramService;
        $text = sprintf(
           "%s's new website address is: %s",
            config('aikopanel.app_name', 'aikopanel'),
            config('aikopanel.app_url')
        );
        $telegramService->sendMessage($message->chat_id, $text, 'markdown');
    }
}
