<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CateringOrder;
use App\Models\BusinessBranch;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Helpers\CommonHelpers;

class SendCateringRatingNotification extends Command
{
    use CommonHelpers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_catering_rating_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send catering rating notification';

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
        \Log::info("Cron is working fine! : SendCateringRatingNotification");
        $start = date('Y-m-d',strtotime(Carbon::now()->addDays(-1)));
        $catering_orders = CateringOrder::whereDate('due_date', $start)
            ->where('rate_notification_at', null)
            ->whereIn('order_status',['booked','completed'])
            ->get();
        foreach ($catering_orders as $corder) {
            if($corder->customer_id && $corder->customer_id > 0){
                //notifications
                $user = User::find($corder->customer_id);
                $title = trans('catering_orders.notification_title');
                $body = trans('catering_orders.notification_body');

                $business_id = BusinessBranch::find($corder->business_branch_id)->business_id;
                if($user) {
                    $nres = $this->sendNotification($user,$title,$body,"rating",$business_id); 
                }
                //save notified at
                $corder->rate_notification_at = Carbon::now();
                $corder->save();         
            }
        }    

    }
}
