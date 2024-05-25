<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Advertisement;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Helpers\CommonHelpers;

class SendNewAdvertisementNotification extends Command
{
    use CommonHelpers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_new_advertisement_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send new advertisement notification';

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
        \Log::info("Cron is working fine! : SendNewAdvertisementNotification");

        $advertisements = Advertisement::whereDate('duration_from', date('Y-m-d'))
        ->where('notified_at', null)
        ->where('status','active')
        ->get();

        //\Log::info("Ad Count:". count($advertisements));
        foreach ($advertisements as $advertisement) {
        //\Log::info("Advertisement id: $advertisement->id");
            
            //notifications
            $users = User::where('user_type','Customer')
                ->where(['verified' => 1, 'status' => 'active'])
                ->get();
            foreach ($users as $user) {
              $title = trans('advertisements.notification_title');
              $body = trans('advertisements.notification_body');
              $nres = $this->sendNotification($user,$title,$body,"advertisement",$advertisement->id);
              // echo '<pre>'; print_r($nres); die;
            }   

            //save notified at
            $advertisement->notified_at = Carbon::now();
            $advertisement->save(); 
        }
    }
}
