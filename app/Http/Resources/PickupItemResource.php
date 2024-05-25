<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class PickupItemResource extends JsonResource
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
            'menu_id' => $this->menu_id,
            'name' => $this->menu_title, //renamed to name from menu_name
            'description' => $this->menu->description, //added
            'photo' => url($this->menu->menu_item_photo), //added
            'price' => $this->menu->currency.' '.$this->menu->price, //added
            // 'menu' => new MenuItemResource($this->menu),
            'quantity' => $this->quantity,
            "currency_symbol" => Setting::get('currency'),
            'unit_price' => number_format($this->unit_price,3, '.', ''),   
            'total_price' => number_format($this->unit_price * $this->quantity,3, '.', ''),
        ];
    }
}