<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\State;
use App\Models\Setting;

class CateringOrderAddressResource extends JsonResource
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
            'city' => State::find($this->city_id)->name,
            'block' => $this->block,
            'street' => $this->street,
            'avenue' => $this->avenue,
            'house_bulding' => $this->house_bulding,
            'floor' => $this->floor,
            'apartment_number' => $this->apartment_number,
            'mobile_number' => $this->mobile_number,
            
        ];
    }
}
