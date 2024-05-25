<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\BusinessBranch;
use App\Models\Business;
use App\Models\Coupon;
use Auth;
use Carbon\Carbon;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $user = Auth::guard('api')->user();

      return [
          'id' => $this->id,
          'customer_id' => $this->customer_id,
          'customer_name' => $user->first_name,
          'customer_phone' => $user->phone_number,
          'reserved_chairs' => $this->reserved_chairs,
          'coupon' => ($this->coupon_id) ? new CouponResource(Coupon::find($this->coupon_id)) : [
                'id' => "",
                'name' => "",
                'code' => "",
                'description' => "",
                'discount' => "",
                'start_date' => "",
                'end_date' => "",
            ],
          'status' => trans('reservations.status.'.$this->status),
          'status_original' => $this->status,
          'check_in_date' => date('Y-m-d',strtotime($this->check_in_date)),
          'check_in_time' => date('h:i A',strtotime($this->check_in_date)),
          'restaurant' => new RestaurantResource(Business::find($this->business_id)),
          'restaurant_branch' => new RestaurantBranchResource(BusinessBranch::find($this->business_branch_id)),
      ];
    }
}
