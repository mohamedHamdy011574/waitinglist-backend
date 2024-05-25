<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Helpers\CommonHelpers;


class SendAdvertisementNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CommonHelpers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $advertisement;
    
    public function __construct($advertisement)
    {
        $this->advertisement = $advertisement;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::where('user_type','Customer')
                    ->where(['verified' => 1, 'status' => 'active'])
                    ->get();
        foreach ($users as $user) {
          $title = trans('advertisements.notification_title');
          $body = trans('advertisements.notification_body');
          $nres = $this->sendNotification($user,$title,$body,"advertisement",$this->advertisement->id);
          // echo '<pre>'; print_r($nres); die;
        }

        \Log::info("Queue worker is working fine! : SendAdvertisementNotificationJob");
    }
}
