<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\Models\User;
use App\Models\Helpers\CommonHelpers;

class SendSponsorNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CommonHelpers;

    public $sponsor;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sponsor)
    {
        $this->sponsor = $sponsor;
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
                  // ->whereIn('id', ['107'])
                  ->get();
        foreach ($users as $user) {
            $title = trans('sponsors.notification_title');
            $body = trans('sponsors.notification_body');
            $nres = $this->sendNotification($user,$title,$body,"sponsor",$this->sponsor->id);
            // echo '<pre>'; print_r($nres); die;
        }

        \Log::info("Queue worker is working fine! : SendSponsorNotificationJob");      
    }
}
