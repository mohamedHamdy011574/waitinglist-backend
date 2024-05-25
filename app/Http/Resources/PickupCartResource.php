<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class PickupCartResource extends JsonResource
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
            'cart_items' => PickupItemResource::collection($this->pickup_cart_items),
            'coupon_id' => $this->coupon_id ?? "",
            'business_branch_id' => $this->business_branch_id,
            'currency_symbol' => Setting::get('currency'),
            'subtotal' => number_format($this->sub_total,3, '.', ''),
            'tax' => number_format($this->taxes,3, '.', ''),
            'discount' => number_format($this->discount,3, '.', ''),
            'total' => number_format($this->grand_total,3, '.', ''),
            'cart_item_count' => $this->item_count,
            'cash_payment_allow'   => $this->restaurant_branch->cash_payment_allow,
            'online_payment_allow' => $this->restaurant_branch->online_payment_allow,
            'wallet_payment_allow' => $this->restaurant_branch->wallet_payment_allow,
        ];
    }
}
