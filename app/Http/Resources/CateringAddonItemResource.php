<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class CateringAddonItemResource extends JsonResource
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
            'name' => $this->catering_addon->addon_name, //renamed to name from 
            'price' => Setting::get('currency').' '.number_format($this->catering_addon->addon_rate,3, '.', ''), //added
            // 'menu' => new MenuItemResource($this->menu),
            'quantity' => $this->quantity,
            'unit_price' => number_format($this->unit_price,3, '.', ''),   
            'total_price' => number_format($this->unit_price * $this->quantity,3, '.', ''),
        ];
    }
}