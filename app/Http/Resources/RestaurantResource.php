<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Helpers\RestaurantHelpers;
use App\Models\FavoriteBusiness;
use App\Models\BusinessCoupon;
use App\Models\Coupon;
use Auth;
use Carbon\Carbon;

class RestaurantResource extends JsonResource
{
    use RestaurantHelpers; 
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $user = Auth::guard('api')->user();
      
      //is favorite?
      $fav = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $this->id, 'type' => 'restaurant'])->get()->count();
      $is_fav = false;
      if($fav) {
        $is_fav = true;
      }

      //coupon availability
      $coupon = false;
      $avail_coupon_ids = BusinessCoupon::where([
                    'business_id' => $this->id,
                    'type' => 'restaurant',
                    ])->pluck('coupon_id')->toArray();
      $avail_coupons = Coupon::whereIn('id',$avail_coupon_ids)                
                  ->whereDate('end_date', '>=', Carbon::now())
                  ->where(['active' => 1])
                  ->get();
      if($avail_coupons->count()) {
        $coupon = true;
      }

      return [
          'id' => $this->id,
          'name' => $this->brand_name,
          'description' => $this->description,
          'link' => $this->link,
          // 'link' => $this->link,
          'status' => $this->vendor->status,
          'banner' => New MediaResource($this->get_restant_banners($this->id, 'single')),
          'coupon' => $coupon,
          'is_fav' => $is_fav,
          'timings' => New WorkingHourResource($this->working_hours),
      ];
    }
}
