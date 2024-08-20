<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TrafficFetchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    protected $server;
    protected $protocol;

    public $tries = 3;
    public $timeout = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data, array $server, $protocol)
    {
        $this->onQueue('traffic_fetch');
        $this->data =$data;
        $this->server = $server;
        $this->protocol = $protocol;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $attempt = 0;
        $maxAttempts = 3;
        while ($attempt < $maxAttempts) {
            try {
                DB::beginTransaction();
                foreach(array_keys($this->data) as $userId){
                    $user = User::lockForUpdate()->find($userId);
                    if (!$user) continue;

                    $user->t = time();
                    $user->u = $user->u + ($this->data[$userId][0] * $this->server['rate']);
                    $user->d = $user->d + ($this->data[$userId][1] * $this->server['rate']);
                    if (!$user->save()) {
                        info("Traffic update failed\nUser ID not recorded: {$userId}\nUpload not recorded: {$user->u}\nDownload not recorded: {$user->d}");
                    }                    
                }
                DB::commit();
                return;
            } catch (\Exception $e) {
                DB::rollback();
                if (strpos($e->getMessage(), '40001') !== false || strpos(strtolower($e->getMessage()), 'deadlock') !== false) {
                    $attempt++;
                    if ($attempt < $maxAttempts) {
                        sleep(5);
                        continue;
                    }
                }
                abort(500, __('User traffic update failed'). $e->getMessage());
            }
        }
    }
}
