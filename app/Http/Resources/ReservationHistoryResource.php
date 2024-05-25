<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\BusinessBranch;
use App\Models\Business;
use App\Models\Coupon;
use Auth;
use Carbon\Carbon;

class ReservationHistoryResource extends JsonResource
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
          'reserved_chairs' => $this->reserved_chairs,
          'status' => trans('reservations.status.'.$this->status),
          'status_original' => $this->status,
          'check_in_date' => date('Y-m-d',strtotime($this->check_in_date)),
          'check_in_time' => date('h:i A',strtotime($this->check_in_date)),
          'restaurant_name' =>  Business::find($this->business_id)->brand_name,
          'restaurant_branch' => BusinessBranch::find($this->business_branch_id)->branch_name,
      ];
    }
}
