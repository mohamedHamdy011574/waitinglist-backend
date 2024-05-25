<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CateringOrderAddress;
use App\Models\Setting;

class CateringCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $address = CateringOrderAddress::where('cart_id', $this->id)->first();
        if($address) {
            $address_data = New CateringOrderAddressResource($address);
        }else{
            $address_data = [
                'id' => "",
                'city' => "",
                'block' => "",
                'street' => "",
                'avenue' => "",
                'house_bulding' => "",
                'floor' => "",
                'apartment_number' => "",
                'mobile_number' => "",
            ];
        }
        return [
            'id'                   => $this->id,
            'cart_items'           => CateringItemResource::collection($this->catering_cart_items),
            'coupon_id'            => $this->coupon_id ?? "",
            'business_branch_id'   => $this->business_branch_id,
            'currency_symbol'      => Setting::get('currency'),
            // 'subtotal'          => number_format($this->sub_total,3, '.', ''),
            'package_total'        => number_format($this->sub_total,3, '.', ''),
            'addons_total'         => number_format($this->addons_total,3, '.', ''),
            'total'                => number_format(($this->sub_total+$this->addons_total),3,'.',''),
            'tax'                  => number_format($this->taxes,3, '.', ''),
            'discount'             => number_format($this->discount,3, '.', ''),
            'total_payable'        => number_format($this->grand_total,3, '.', ''),
            'cart_item_count'      => $this->item_count,
            'address'              => $address_data,
            'cash_payment_allow'   => $this->restaurant_branch->cash_payment_allow,
            'online_payment_allow' => $this->restaurant_branch->online_payment_allow,
            'wallet_payment_allow' => $this->restaurant_branch->wallet_payment_allow,
        ];
    }
}
