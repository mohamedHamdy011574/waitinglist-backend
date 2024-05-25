<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class PickupOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_items' => PickupItemResource::collection($this->pickup_order_items),
            'coupon_id' => $this->coupon_id ?? "",
            'business_branch_id' => $this->business_branch_id,
            'coupon' => $this->coupon ? new CouponResource($this->coupon) :  
                    [
                'id' => "",
                'name' => "",
                'code' => "",
                'description' => "",
                'discount' => "",
                'start_date' => "",
                'end_date' => "",
            ],
            'currency_symbol' => Setting::get('currency'),
            'subtotal' => number_format($this->sub_total,3, '.', ''),
            'tax' => number_format($this->taxes,3, '.', ''),
            'discount' => number_format($this->discount,3, '.', ''),
            'total' => number_format($this->grand_total,3, '.', ''),
            'order_item_count' => $this->item_count,
            'pickup_date' => $this->pickup_date,
            'due_date' => $this->due_date,
            'due_time' => $this->due_time,
            'payment_mode' => $this->payment_mode,
            'payment_status' => $this->payment_status,
            'order_status' => $this->order_status
        ];
    }
}
