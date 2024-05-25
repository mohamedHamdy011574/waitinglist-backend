<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BloggerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
         return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'blogger_photo' => asset($this->blogger_photo),
            'blogger_name' => ucfirst($this->blogger_name),
        ];
    }
}
