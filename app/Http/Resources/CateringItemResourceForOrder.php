<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class CateringItemResourceForOrder extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $photo = @$this->catering_package->photos[0]->image;
        if($photo == '') {
            $photo = 'uploads/default.png';
        }

        $addon_cart = [
            'id' => "",
            'addon_cart_items' => [],
            'total' => "",
            'cart_item_count' => ""
        ];



        if(isset($this->catering_addon_order)){
            // if(count($this->catering_addon_orders)){
                $addon_cart = new CateringAddonOrderResource($this->catering_addon_order); 
            // }
        }

        // return $this->catering_addon_orders;
        // return $this->catering_addon_order;

        return [
            'id' => $this->id,
            'cat_packg_id' => $this->cat_packg_id,
            'name' => $this->catering_package->package_name, //renamed to name from menu_name
            'food_serving' => $this->catering_package->food_serving, //added
            'price' => $this->catering_package->currency.' '.$this->catering_package->price, //added
            // 'menu' => new MenuItemResource($this->menu),
            'quantity' => $this->quantity,
            "currency_symbol" => Setting::get('currency'),
            'unit_price' => number_format($this->unit_price,3, '.', ''),   
            'addons_price' => number_format($this->addons_price,3, '.', ''), 
            'total_price' => number_format(($this->unit_price * $this->quantity + @$this->catering_addon_cart->total),3, '.', ''),
            'addon_cart' => $addon_cart,
            'photo' => url($photo),
        ];
    }
}