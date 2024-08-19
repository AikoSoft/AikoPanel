<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use App\Models\User;
use App\Utils\Helper;
use Illuminate\Support\Facades\DB;

class AikoPanelInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aikopanel:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aikopanel install';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info("    _                       _  __       U  ___ u ");
            $this->info("   /\"\\  u       ___        |\"|/ /        \\/\"_ \\/ ");
            $this->info(" \\/ _ \\/        |\"_|       | ' /         | | | | ");
            $this->info(" / ___ \\        | |      U/| . \\\\u   .-,_| |_| | ");
            $this->info("/_/   \\_\\     U/| |\\u      |_|\\_\\     \\_)-\\___/  ");
            $this->info(" \\\    >>  .-,_|___|_,-.  ,-,>> \\\\,-.       \\\\    ");
            $this->info(" (__) (__)  \\_)-' '-(_/    \\.)   (_/       (__)   ");
            $this->info("                                                  ");
            $this->info("--------------------------------------------------");
            if (\File::exists(base_path() . '/.env')) {
                $securePath = config('aikopanel.secure_path', config('aikopanel.frontend_admin_path', hash('crc32b', config('app.key'))));
                $this->info("Visit http(s)://yoursite/{$securePath} to enter the admin panel. You can change your password in the user center.");
                abort(500, __('If you need to reinstall, please delete the .env file in the directory'));
            }

            if (!copy(base_path() . '/.env.example', base_path() . '/.env')) {
                abort(500, __('Failed to copy .env.example file'));
            }
            $this->saveToEnv([
                'APP_KEY' => 'base64:' . base64_encode(Encrypter::generateKey('AES-256-CBC')),
                'DB_HOST' => $this->ask('Please enter the database address（Default: localhost）', 'localhost'),
                'DB_DATABASE' => $this->ask('Please enter the database name'),
                'DB_USERNAME' => $this->ask('Please enter the database username'),
                'DB_PASSWORD' => $this->ask('Please enter the database password')
            ]);
            \Artisan::call('config:clear');
            \Artisan::call('config:cache');
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                abort(500, __('Failed to connect to the database'));
            }
            $file = \File::get(base_path() . '/database/install.sql');
            if (!$file) {
                abort(500, __('Database file does not exist'));
            }
            $sql = str_replace("\n", "", $file);
            $sql = preg_split("/;/", $sql);
            if (!is_array($sql)) {
                abort(500, __('Database file format error'));
            }
            $this->info('Please wait for the database to be imported ...');
            foreach ($sql as $item) {
                try {
                    DB::select(DB::raw($item));
                } catch (\Exception $e) {
                }
            }
            $this->info('Database import completed');
            $email = '';
            while (!$email) {
                $email = $this->ask('Please enter the email of the administrator');
            }
            $password = Helper::guid(false);
            if (!$this->registerAdmin($email, $password)) {
                abort(500, __('Failed to register administrator account, please try again later'));
            }

            $this->info('All done!');
            $this->info("Email：{$email}");
            $this->info("Pass：{$password}");

            $defaultSecurePath = hash('crc32b', config('app.key'));
            $this->info("Visit http(s)://yoursite/{$defaultSecurePath} to enter the admin panel. You can change your password in the user center.");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function registerAdmin($email, $password)
    {
        $user = new User();
        $user->email = $email;
        if (strlen($password) < 8) {
            abort(500, __('Password must be greater than 8 digits'));
        }
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->uuid = Helper::guid(true);
        $user->token = Helper::guid();
        $user->is_admin = 1;
        return $user->save();
    }

    private function saveToEnv($data = [])
    {
        function set_env_var($key, $value)
        {
            if (! is_bool(strpos($value, ' '))) {
                $value = '"' . $value . '"';
            }
            $key = strtoupper($key);

            $envPath = app()->environmentFilePath();
            $contents = file_get_contents($envPath);

            preg_match("/^{$key}=[^\r\n]*/m", $contents, $matches);

            $oldValue = count($matches) ? $matches[0] : '';

            if ($oldValue) {
                $contents = str_replace("{$oldValue}", "{$key}={$value}", $contents);
            } else {
                $contents = $contents . "\n{$key}={$value}\n";
            }

            $file = fopen($envPath, 'w');
            fwrite($file, $contents);
            return fclose($file);
        }
        foreach($data as $key => $value) {
            set_env_var($key, $value);
        }
        return true;
    }
}
