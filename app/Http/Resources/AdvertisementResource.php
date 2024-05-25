<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementResource extends JsonResource
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
            'name' => $this->name,
            'video' => asset($this->video),
            'duration_from' => $this->duration_from,
            'duration_to' => $this->duration_to,
            // 'status' => $this->status,
        ];
    }
}
