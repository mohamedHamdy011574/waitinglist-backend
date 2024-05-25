<?php

namespace App\Models;
use App\Models\SubscriptionPackage;


use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['vendor_id', 'sub_package_id','for_restaurant', 'for_catering', 'for_food_truck', 'reservation', 'waiting_list', 'pickup', 'branches_include', 'subscription_period', 'package_price', 'currency', 'puchase_date', 'package_end_date', 'status', 'payment_mode', 'payment_status'];


    public static function get_subscription($vendor_id='', $stype='') {
    		$packages = SubscriptionPackage::where(['package_for'=>$stype])->pluck('id')->toArray();
    		$subscription = Subscription::whereIn('package_id', $packages)->where(['vendor_id' => $vendor_id])->first();
    		return $subscription;
    }


    public static function get_package_info($package_id='', $type='') {
    		$service = SubscriptionPackage::where(['id' => $package_id, 'package_for' => $type, 'status' => 'active'])->first();
    		if($service) {
    			return [
    				'package_id' => $package_id,
    				'type' => $type,
    				'services' => explode(',', $service->service_include),
    				'branches' => $service->branches_include,
    			];
    		}
    		return '';
    		/*return [
    		'services' => $package_id),
    		'branches' => $type,
    		];*/
    }

    public function subscription_package(){
      return $this->hasMany(SubscriptionPackage::class, 'package_id');
    }
}