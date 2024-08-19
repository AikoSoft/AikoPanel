<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AikoPanelUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aikopanel:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AikoPanel update';

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
        \Artisan::call('config:cache');
        DB::connection()->getPdo();
        $file = \File::get(base_path() . '/database/update.sql');
        if (!$file) {
            abort(500, __('Database update file not found'));
        }
        $sql = str_replace("\n", "", $file);
        $sql = preg_split("/;/", $sql);
        if (!is_array($sql)) {
            abort(500, __('Database update file is empty'));
        }
        $this->info('Importing database, please wait...');
        foreach ($sql as $item) {
            if (!$item) continue;
            try {
                DB::select(DB::raw($item));
            } catch (\Exception $e) {
            }
        }
        \Artisan::call('horizon:terminate');
        $this->info('Update completed, the queue service has been restarted, you do not need to do anything.');
    }
}
