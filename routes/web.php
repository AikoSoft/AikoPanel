<?php

use App\Services\ThemeService;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    if (config('aikopanel.app_url') && config('aikopanel.safe_mode_enable', 0)) {
        if ($request->server('HTTP_HOST') !== parse_url(config('aikopanel.app_url'))['host']) {
            abort(403);
        }
    }
    $renderParams = [
        'title' => config('aikopanel.app_name', 'aikopanel'),
        'theme' => config('aikopanel.frontend_theme', 'default'),
        'version' => config('app.version'),
        'description' => config('aikopanel.app_description', 'aikopanel is best'),
        'logo' => config('aikopanel.logo')
    ];

    if (!config("theme.{$renderParams['theme']}")) {
        $themeService = new ThemeService($renderParams['theme']);
        $themeService->init();
    }

    $renderParams['theme_config'] = config('theme.' . config('aikopanel.frontend_theme', 'default'));
    return view('theme::' . config('aikopanel.frontend_theme', 'default') . '.dashboard', $renderParams);
});

//TODO:: 兼容
Route::get('/' . config('aikopanel.secure_path', config('aikopanel.frontend_admin_path', hash('crc32b', config('app.key')))), function () {
    return view('admin', [
        'title' => config('aikopanel.app_name', 'aikopanel'),
        'theme_sidebar' => config('aikopanel.frontend_theme_sidebar', 'light'),
        'theme_header' => config('aikopanel.frontend_theme_header', 'dark'),
        'theme_color' => config('aikopanel.frontend_theme_color', 'default'),
        'background_url' => config('aikopanel.frontend_background_url'),
        'version' => config('app.version'),
        'logo' => config('aikopanel.logo'),
        'secure_path' => config('aikopanel.secure_path', config('aikopanel.frontend_admin_path', hash('crc32b', config('app.key'))))
    ]);
});

if (!empty(config('aikopanel.subscribe_path'))) {
    Route::get(config('aikopanel.subscribe_path'), 'V1\\Client\\ClientController@subscribe')->middleware('client');
}