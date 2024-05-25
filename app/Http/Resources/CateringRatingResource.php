<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CateringRatingResource extends JsonResource
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
            'service_rating' => $this->quality_rating,
            'quality_rating' => $this->quality_rating,
            'on_time_rating' => $this->on_time_rating,
            'presentation_rating' => $this->presentation_rating,
        ];
    }
}
