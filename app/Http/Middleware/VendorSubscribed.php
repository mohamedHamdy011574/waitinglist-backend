<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Subscription;
use Carbon\Carbon;

class VendorSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if($user->user_type == 'Vendor'){

            if($user->status != 'active') {
                return redirect()->route('profile.index')->with('error', trans('vendors.you_are_inactive'));
            }
            
            $sub = Subscription::where(['vendor_id'=>$user->id,'status'=>'active','payment_status' => 'paid'])->first();
            if($sub != null){
                $current_date = Carbon::now();
                $package_end_date  = Carbon::parse($sub->package_end_date);
                
                if($current_date->greaterThan($package_end_date)) {
                    
                    // Redirect to Subscriptions
                    if($request->user()->user_type == 'Vendor') {
                        return redirect()->route('my_subscription')->with('error', trans('common.vendor_need_renew_subscription'));
                    }
                } else {

                    //Redirect to dashboard
                    return $next($request);
                }
                
            } else {
                
                //ERROR: REDIRECT TO SUBSCRIPTIONS
                // Redirect to Subscriptions
                    if($request->user()->user_type == 'Vendor') {
                        return redirect()->route('my_subscription')->with('error', trans('common.vendor_not_subscribed'));
                    }
            }
        }

        return $next($request);
    }
}
