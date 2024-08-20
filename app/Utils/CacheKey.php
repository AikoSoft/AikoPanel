<?php

namespace App\Utils;

class CacheKey
{
    const KEYS = [
        'EMAIL_VERIFY_CODE' => 'Email verification code',
        'LAST_SEND_EMAIL_VERIFY_TIMESTAMP' => 'Timestamp of the last sent email verification code',
        'SERVER_VMESS_ONLINE_USER' => 'VMess node online users',
        'SERVER_VMESS_LAST_CHECK_AT' => 'Last check time of the VMess node',
        'SERVER_VMESS_LAST_PUSH_AT' => 'Last push time of the VMess node',
        'SERVER_TROJAN_ONLINE_USER' => 'Trojan node online users',
        'SERVER_TROJAN_LAST_CHECK_AT' => 'Last check time of the Trojan node',
        'SERVER_TROJAN_LAST_PUSH_AT' => 'Last push time of the Trojan node',
        'SERVER_SHADOWSOCKS_ONLINE_USER' => 'Shadowsocks node online users',
        'SERVER_SHADOWSOCKS_LAST_CHECK_AT' => 'Last check time of the Shadowsocks node',
        'SERVER_SHADOWSOCKS_LAST_PUSH_AT' => 'Last push time of the Shadowsocks node',
        'SERVER_HYSTERIA_ONLINE_USER' => 'Hysteria node online users',
        'SERVER_HYSTERIA_LAST_CHECK_AT' => 'Last check time of the Hysteria node',
        'SERVER_HYSTERIA_LAST_PUSH_AT' => 'Last push time of the Hysteria node',
        'SERVER_VLESS_ONLINE_USER' => 'VLESS node online users',
        'SERVER_VLESS_LAST_CHECK_AT' => 'Last check time of the VLESS node',
        'SERVER_VLESS_LAST_PUSH_AT' => 'Last push time of the VLESS node',
        'TEMP_TOKEN' => 'Temporary token',
        'LAST_SEND_EMAIL_REMIND_TRAFFIC' => 'Last sent traffic email reminder',
        'SCHEDULE_LAST_CHECK_AT' => 'Last check time of the scheduled task',
        'REGISTER_IP_RATE_LIMIT' => 'Registration rate limit',
        'LAST_SEND_LOGIN_WITH_MAIL_LINK_TIMESTAMP' => 'Timestamp of the last sent login link',
        'PASSWORD_ERROR_LIMIT' => 'Password error limit',
        'USER_SESSIONS' => 'User sessions',
        'FORGET_REQUEST_LIMIT' => 'Password recovery request limit'
    ];

    public static function get(string $key, $uniqueValue)
    {
        if (!in_array($key, array_keys(self::KEYS))) {
            abort(500, 'key is not in cache key list');
        }
        return $key . '_' . $uniqueValue;
    }
}
