<?php
namespace App\Services;


use App\Jobs\SendEmailJob;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TicketService {
    public function reply($ticket, $message, $userId)
    {
        DB::beginTransaction();
        $ticketMessage = TicketMessage::create([
            'user_id' => $userId,
            'ticket_id' => $ticket->id,
            'message' => $message
        ]);
        if ($userId !== $ticket->user_id) {
            $ticket->reply_status = 0;
        } else {
            $ticket->reply_status = 1;
        }
        if (!$ticketMessage || !$ticket->save()) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return $ticketMessage;
    }

    public function replyByAdmin($ticketId, $message, $userId):void
    {
        $ticket = Ticket::where('id', $ticketId)
            ->first();
        if (!$ticket) {
            abort(500, __('Work order does not exist'));
        }
        $ticket->status = 0;
        DB::beginTransaction();
        $ticketMessage = TicketMessage::create([
            'user_id' => $userId,
            'ticket_id' => $ticket->id,
            'message' => $message
        ]);
        if ($userId !== $ticket->user_id) {
            $ticket->reply_status = 0;
        } else {
            $ticket->reply_status = 1;
        }
        if (!$ticketMessage || !$ticket->save()) {
            DB::rollback();
            abort(500, __('Work order reply failed'));
        }
        DB::commit();
        $this->sendEmailNotify($ticket, $ticketMessage);
    }

    // 半小时内不再重复通知
    private function sendEmailNotify(Ticket $ticket, TicketMessage $ticketMessage)
    {
        $user = User::find($ticket->user_id);
        $cacheKey = 'ticket_sendEmailNotify_' . $ticket->user_id;
        if (!Cache::get($cacheKey)) {
            Cache::put($cacheKey, 1, 1800);
            SendEmailJob::dispatch([
                'email' => $user->email,
                'subject' => 'You have received a reply to your ticket on ' . config('aikopanel.app_name', 'aikopanel'),
                'template_name' => 'notify',
                'template_value' => [
                    'name' => config('aikopanel.app_name', 'aikopanel'),
                    'url' => config('aikopanel.app_url'),
                    'content' => "Subject: {$ticket->subject}\r\nReply content: {$ticketMessage->message}"
                ]
            ]);
        }
        
}
