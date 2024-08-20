<?php

namespace App\Plugins\Telegram\Commands;

use App\Models\User;
use App\Plugins\Telegram\Telegram;

class Bind extends Telegram {
    public $command = '/bind';
    public $description = 'Bind Telegram account to website';

    public function handle($message, $match = []) {
        if (!$message->is_private) return;
        if (!isset($message->args[0])) {
            abort(500, __('The parameters are incorrect, please send with the subscription address'));
        }
        $subscribeUrl = $message->args[0];
        $subscribeUrl = parse_url($subscribeUrl);
        parse_str($subscribeUrl['query'], $query);
        $token = $query['token'];
        if (!$token) {
            abort(500, __('Subscription address is invalid'));
        }
        $user = User::where('token', $token)->first();
        if (!$user) {
            abort(500, __('User does not exist'));
        }
        if ($user->telegram_id) {
            abort(500, __('This account has been bound to a Telegram account'));
        }
        $user->telegram_id = $message->chat_id;
        if (!$user->save()) {
            abort(500, __('Setup failed'));
        }
        $telegramService = $this->telegramService;
        $telegramService->sendMessage($message->chat_id, 'Binding successful');
    }
}
