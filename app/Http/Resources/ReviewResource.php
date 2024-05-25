<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'id'         => $this->id,
            'review'     => $this->review,
            'added_by'   => $this->customer->first_name .' '. $this->customer->last_name,
            'added_date' => date('jS M Y',strtotime($this->created_at)),
            'added_time' => date('H:i A',strtotime($this->created_at))
        ];
    }
}
