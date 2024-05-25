<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Helpers\RestaurantHelpers;
use App\Models\FavoriteBusiness;
use App\Models\Coupon;
use App\Models\BusinessCoupon;
use Auth;
use Carbon\Carbon;

class RestaurantDetailResource extends JsonResource
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

      //branches
      $branches = [];
      foreach($this->restaurant_branches as $r_b) {
        if($r_b->state_id == $this->selected_city_id) {
          $selected = true;
        }else{
          $selected = false;
        }
        array_push($branches, [
          'id' => $r_b->id,
          'business_id' => $r_b->business_id,
          'name' => $r_b->branch_name,
          'address' => $r_b->address,
          'latitude' => ($r_b->latitude) ? $r_b->latitude : '',
          'longitude' => ($r_b->longitude) ? $r_b->longitude : '',
          'city' => $r_b->state_id,
          'selected' => $selected,
          'reservation_allow' => ($r_b->reservation_allow) ? true : false,
          'waiting_list_allow' => ($r_b->waiting_list_allow) ? true : false,
          'pickup_allow' => ($r_b->pickup_allow) ? true : false,
        ]);
      }
      // $branches = $this->branches;

      //coupons 
      $coupons_ids = BusinessCoupon::where([
                    'business_id' => $this->id,
                    'type' => 'restaurant',
                    ])->pluck('coupon_id')->toArray();
      $coupons = Coupon::whereIn('id',$coupons_ids)                
                  ->whereDate('end_date', '>=', Carbon::now())
                  ->where(['active' => 1])
                  ->get();


      
      //current status
      $current_status = $this->working_status;

      return [
          'id' => $this->id,
          'name' => $this->brand_name,
          'description' => $this->description,
          'link' => $this->link,
          // 'link' => $this->link,
          'status' => $this->vendor->status,
          'banners' => MediaResource::collection($this->get_restant_banners($this->id)),
          'coupons' => CouponResource::collection($coupons),
          'is_fav' => $is_fav,
          'branches' => $branches,
          'timings' => New WorkingHourResource($this->working_hours),
          'current_status' => $current_status,
      ];
    }
}
