<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'sponsor_video' => asset($this->video),
            'first_name' => ucfirst($this->first_name),
            'last_name' => ucfirst($this->last_name),
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'duration_from' => $this->duration_from,
            'duration_to' => $this->duration_to,
            'payment_status' => $this->payment_status,
            // 'status' => $this->status,
        ];
    }
}
