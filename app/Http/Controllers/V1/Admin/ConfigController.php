<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfigSave;
use App\Jobs\SendEmailJob;
use App\Services\TelegramService;
use App\Utils\Dict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class ConfigController extends Controller
{
    public function getEmailTemplate()
    {
        $path = resource_path('views/mail/');
        $files = array_map(function ($item) use ($path) {
            return str_replace($path, '', $item);
        }, glob($path . '*'));
        return response([
            'data' => $files
        ]);
    }

    public function getThemeTemplate()
    {
        $path = public_path('theme/');
        $files = array_map(function ($item) use ($path) {
            return str_replace($path, '', $item);
        }, glob($path . '*'));
        return response([
            'data' => $files
        ]);
    }

    public function testSendMail(Request $request)
    {
        $obj = new SendEmailJob([
            'email' => $request->user['email'],
            'subject' => 'This is aikopanel test email',
            'template_name' => 'notify',
            'template_value' => [
                'name' => config('aikopanel.app_name', 'AikoPanel'),
                'content' => 'This is aikopanel test email',
                'url' => config('aikopanel.app_url')
            ]
        ]);
        return response([
            'data' => true,
            'log' => $obj->handle()
        ]);
    }

    public function setTelegramWebhook(Request $request)
    {
        $hookUrl = url('/api/v1/guest/telegram/webhook?access_token=' . md5(config('aikopanel.telegram_bot_token', $request->input('telegram_bot_token'))));
        $telegramService = new TelegramService($request->input('telegram_bot_token'));
        $telegramService->getMe();
        $telegramService->setWebhook($hookUrl);
        return response([
            'data' => true
        ]);
    }

    public function fetch(Request $request)
    {
        $key = $request->input('key');
        $data = [
            'invite' => [
                'invite_force' => (int)config('aikopanel.invite_force', 0),
                'invite_commission' => config('aikopanel.invite_commission', 10),
                'invite_gen_limit' => config('aikopanel.invite_gen_limit', 5),
                'invite_never_expire' => config('aikopanel.invite_never_expire', 0),
                'commission_first_time_enable' => config('aikopanel.commission_first_time_enable', 1),
                'commission_auto_check_enable' => config('aikopanel.commission_auto_check_enable', 1),
                'commission_withdraw_limit' => config('aikopanel.commission_withdraw_limit', 100),
                'commission_withdraw_method' => config('aikopanel.commission_withdraw_method', Dict::WITHDRAW_METHOD_WHITELIST_DEFAULT),
                'withdraw_close_enable' => config('aikopanel.withdraw_close_enable', 0),
                'commission_distribution_enable' => config('aikopanel.commission_distribution_enable', 0),
                'commission_distribution_l1' => config('aikopanel.commission_distribution_l1'),
                'commission_distribution_l2' => config('aikopanel.commission_distribution_l2'),
                'commission_distribution_l3' => config('aikopanel.commission_distribution_l3')
            ],
            'site' => [
                'logo' => config('aikopanel.logo'),
                'force_https' => (int)config('aikopanel.force_https', 0),
                'stop_register' => (int)config('aikopanel.stop_register', 0),
                'app_name' => config('aikopanel.app_name', 'aikopanel'),
                'app_description' => config('aikopanel.app_description', 'aikopanel is best!'),
                'app_url' => config('aikopanel.app_url'),
                'subscribe_url' => config('aikopanel.subscribe_url'),
                'subscribe_path' => config('aikopanel.subscribe_path'),
                'try_out_plan_id' => (int)config('aikopanel.try_out_plan_id', 0),
                'try_out_hour' => (int)config('aikopanel.try_out_hour', 1),
                'tos_url' => config('aikopanel.tos_url'),
                'currency' => config('aikopanel.currency', 'CNY'),
                'currency_symbol' => config('aikopanel.currency_symbol', '¥'),
            ],
            'subscribe' => [
                'plan_change_enable' => (int)config('aikopanel.plan_change_enable', 1),
                'reset_traffic_method' => (int)config('aikopanel.reset_traffic_method', 0),
                'surplus_enable' => (int)config('aikopanel.surplus_enable', 1),
                'new_order_event_id' => (int)config('aikopanel.new_order_event_id', 0),
                'renew_order_event_id' => (int)config('aikopanel.renew_order_event_id', 0),
                'change_order_event_id' => (int)config('aikopanel.change_order_event_id', 0),
                'show_info_to_server_enable' => (int)config('aikopanel.show_info_to_server_enable', 0)
            ],
            'frontend' => [
                'frontend_theme' => config('aikopanel.frontend_theme', 'aikopanel'),
                'frontend_theme_sidebar' => config('aikopanel.frontend_theme_sidebar', 'light'),
                'frontend_theme_header' => config('aikopanel.frontend_theme_header', 'dark'),
                'frontend_theme_color' => config('aikopanel.frontend_theme_color', 'default'),
                'frontend_background_url' => config('aikopanel.frontend_background_url'),
            ],
            'server' => [
                'server_token' => config('aikopanel.server_token'),
                'server_pull_interval' => config('aikopanel.server_pull_interval', 60),
                'server_push_interval' => config('aikopanel.server_push_interval', 60),
                'device_limit_mode' => config('aikopanel.device_limit_mode', 0)
            ],
            'email' => [
                'email_template' => config('aikopanel.email_template', 'default'),
                'email_host' => config('aikopanel.email_host'),
                'email_port' => config('aikopanel.email_port'),
                'email_username' => config('aikopanel.email_username'),
                'email_password' => config('aikopanel.email_password'),
                'email_encryption' => config('aikopanel.email_encryption'),
                'email_from_address' => config('aikopanel.email_from_address')
            ],
            'telegram' => [
                'telegram_bot_enable' => config('aikopanel.telegram_bot_enable', 0),
                'telegram_bot_token' => config('aikopanel.telegram_bot_token'),
                'telegram_discuss_link' => config('aikopanel.telegram_discuss_link')
            ],
            'app' => [
                'windows_version' => config('aikopanel.windows_version'),
                'windows_download_url' => config('aikopanel.windows_download_url'),
                'macos_version' => config('aikopanel.macos_version'),
                'macos_download_url' => config('aikopanel.macos_download_url'),
                'android_version' => config('aikopanel.android_version'),
                'android_download_url' => config('aikopanel.android_download_url')
            ],
            'safe' => [
                'email_verify' => (int)config('aikopanel.email_verify', 0),
                'safe_mode_enable' => (int)config('aikopanel.safe_mode_enable', 0),
                'secure_path' => config('aikopanel.secure_path', config('aikopanel.frontend_admin_path', hash('crc32b', config('app.key')))),
                'email_whitelist_enable' => (int)config('aikopanel.email_whitelist_enable', 0),
                'email_whitelist_suffix' => config('aikopanel.email_whitelist_suffix', Dict::EMAIL_WHITELIST_SUFFIX_DEFAULT),
                'email_gmail_limit_enable' => config('aikopanel.email_gmail_limit_enable', 0),
                'recaptcha_enable' => (int)config('aikopanel.recaptcha_enable', 0),
                'recaptcha_key' => config('aikopanel.recaptcha_key'),
                'recaptcha_site_key' => config('aikopanel.recaptcha_site_key'),
                'register_limit_by_ip_enable' => (int)config('aikopanel.register_limit_by_ip_enable', 0),
                'register_limit_count' => config('aikopanel.register_limit_count', 3),
                'register_limit_expire' => config('aikopanel.register_limit_expire', 60),
                'password_limit_enable' => (int)config('aikopanel.password_limit_enable', 1),
                'password_limit_count' => config('aikopanel.password_limit_count', 5),
                'password_limit_expire' => config('aikopanel.password_limit_expire', 60)
            ]
        ];
        if ($key && isset($data[$key])) {
            return response([
                'data' => [
                    $key => $data[$key]
                ]
            ]);
        };
        // TODO: default should be in Dict
        return response([
            'data' => $data
        ]);
    }

    public function save(ConfigSave $request)
    {
        $data = $request->validated();
        $config = config('aikopanel');
        foreach (ConfigSave::RULES as $k => $v) {
            if (!in_array($k, array_keys(ConfigSave::RULES))) {
                unset($config[$k]);
                continue;
            }
            if (array_key_exists($k, $data)) {
                $config[$k] = $data[$k];
            }
        }
        $data = var_export($config, 1);
        if (!File::put(base_path() . '/config/aikopanel.php', "<?php\n return $data ;")) {
            abort(500, __('Save failed'));
        }
        if (function_exists('opcache_reset')) {
            if (opcache_reset() === false) {
                abort(500, __('Cache clearing failed, please uninstall or check opcache configuration status'));
            }
        }
        Artisan::call('config:cache');
        if(Cache::has('WEBMANPID')) {
            $pid = Cache::get('WEBMANPID');
            Cache::forget('WEBMANPID');
            return response([
                'data' => posix_kill($pid, 15)
            ]);
        }
        return response([
            'data' => true
        ]);
    }
}
