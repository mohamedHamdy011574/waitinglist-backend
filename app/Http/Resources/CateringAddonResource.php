<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CateringOrderAddress;
use App\Models\Setting;

class CateringAddonResource extends JsonResource
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
            'addon_name' => $this->addon_name,
            'description' => $this->description,
            'price' => Setting::get('currency').' '.number_format($this->addon_rate,3, '.', ''),
            'count' => 0,
        ];
    }
}
