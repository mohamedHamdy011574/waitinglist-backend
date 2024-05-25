<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPackage;
use App\Models\Subscription;
use DB, Auth;
use Carbon\Carbon;


class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:my-subscription', ['only' => ['my_subscription', 'choose_subscription', 'save_chosen_subscription']]);
    }

    //my_subscription
    public function my_subscription()
    {
        $page_title = trans('my_subscriptions.heading');
        $vendor = Auth::user();
        $subscription = Subscription::where('vendor_id', $vendor->id)->first();
        if($subscription){
          $subscription_packages = SubscriptionPackage::whereIn('status', ['active','inactive'])->latest()->get();
        }else {
          $subscription_packages = SubscriptionPackage::whereIn('status', ['active'])->latest()->get();
        }
        return view('admin.my_subscriptions.my_subscription',compact('page_title', 'subscription', 'subscription_packages','vendor'));
    }

    //choose_subscription
    public function choose_subscription()
    {
        $page_title = trans('my_subscriptions.choose_subscription');
        $vendor = Auth::user();
        $subscription = Subscription::where('vendor_id', $vendor->id)->first();
        $subscription_packages = SubscriptionPackage::where(['status' => 'active'])->latest()->get();
        return view('admin.my_subscriptions.choose_subscription',compact('page_title', 'subscription', 'subscription_packages','vendor'));
    }

    public function save_chosen_subscription(Request $request) {

        $validator= $request->validate([
            'subscription_package' => ['required', 'exists:subscription_packages,id'],
        ]);

        $vendor = Auth::user();
        //saving subscription
          $subscription_data = [];
          $r_package = SubscriptionPackage::find($request->subscription_package);
          if(!$r_package) {
            return redirect()->back()->withInput()->with('error',trans('common.no_data'));
          }

          $subscribed = Subscription::where('vendor_id', $vendor->id)->first();
          if($subscribed) {
            return redirect()->route('my_subscription')->with('success',trans('vendors.already_subscribed'));
          }

          DB::beginTransaction();
          $subscription_data[] = [
            'sub_package_id' => $request->subscription_package,
            'vendor_id' => $vendor->id,

            'for_restaurant' => $r_package->for_restaurant,
            'for_catering' => $r_package->for_catering,
            'for_food_truck' => $r_package->for_food_truck,
            'reservation' => $r_package->reservation,
            'waiting_list' => $r_package->waiting_list,
            'pickup' => $r_package->pickup,
            'branches_include' => $r_package->branches_include,
            'subscription_period' => $r_package->subscription_period,
            'package_price' => $r_package->package_price,
            'currency' => $r_package->currency,
            'purchase_date' => date('Y-m-d'),
            'package_end_date' => Carbon::today()->addYears($r_package->subscription_period),
            'payment_mode' => 'manual',
            'payment_status' => 'paid',
          ];

          Subscription::insert($subscription_data);

          DB::commit();
          return redirect()->route('my_subscription')->with('success',trans('vendors.subscription_added'));
    }
}
   