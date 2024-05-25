<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sponsor;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Helpers\CommonHelpers;

class SendSponsorAdNotification extends Command
{
    use CommonHelpers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_sponsor_ad_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send sponsor ad notification';

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
        \Log::info("Cron is working fine! : SendSponsorAdNotification");

        $sponsors = Sponsor::whereDate('duration_from', date('Y-m-d'))
        ->where('notified_at', null)
        ->where('status','active')
        ->get();

        //\Log::info("Ad Count:". count($sponsors));
        foreach ($sponsors as $sponsor) {
        //\Log::info("Sponsor id: $sponsor->id");
            
            //notifications
            $users = User::where('user_type','Customer')
                ->where(['verified' => 1, 'status' => 'active'])
                ->get();
            foreach ($users as $user) {
              $title = trans('sponsors.notification_title');
              $body = trans('sponsors.notification_body');
              $nres = $this->sendNotification($user,$title,$body,"sponsor",$sponsor->id);
              // echo '<pre>'; print_r($nres); die;
            }   

            //save notified at
            $sponsor->notified_at = Carbon::now();
            $sponsor->save(); 
        }
    }
}
