<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Support\Facades\Cache;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authorization = $request->input('auth_data') ?? $request->header('authorization');
        if (!$authorization) abort(403, __('Not logged in or login has expired'));

        $user = AuthService::decryptAuthData($authorization);
        if (!$user) abort(403, __('Not logged in or login has expired'));
        $request->merge([
            'user' => $user
        ]);
        return $next($request);
    }
}
