<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\MailLog;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $params;

    public $tries = 3;
    public $timeout = 10;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params, $queue = 'send_email')
    {
        $this->onQueue($queue);
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (config('aikopanel.email_host')) {
            Config::set('mail.host', config('aikopanel.email_host', env('mail.host')));
            Config::set('mail.port', config('aikopanel.email_port', env('mail.port')));
            Config::set('mail.encryption', config('aikopanel.email_encryption', env('mail.encryption')));
            Config::set('mail.username', config('aikopanel.email_username', env('mail.username')));
            Config::set('mail.password', config('aikopanel.email_password', env('mail.password')));
            Config::set('mail.from.address', config('aikopanel.email_from_address', env('mail.from.address')));
            Config::set('mail.from.name', config('aikopanel.app_name', 'aikopanel'));
        }
        $params = $this->params;
        $email = $params['email'];
        $subject = $params['subject'];
        $params['template_name'] = 'mail.' . config('aikopanel.email_template', 'default') . '.' . $params['template_name'];
        try {
            Mail::send(
                $params['template_name'],
                $params['template_value'],
                function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                }
            );
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $log = [
            'email' => $params['email'],
            'subject' => $params['subject'],
            'template_name' => $params['template_name'],
            'error' => isset($error) ? $error : NULL
        ];

        MailLog::create($log);
        $log['config'] = config('mail');
        return $log;
    }
}
