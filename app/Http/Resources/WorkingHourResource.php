<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHourResource extends JsonResource
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
            // 'id' => $this->id,
            'sunday_serving' => $this->sunday_serving,
            'monday_serving' => $this->monday_serving,
            'tuesday_serving' => $this->tuesday_serving,
            'wednesday_serving' => $this->wednesday_serving,
            'thursday_serving' => $this->thursday_serving,
            'friday_serving' => $this->friday_serving,
            'saturday_serving' => $this->saturday_serving,
            'from_time' => date("h:i a", strtotime($this->from_time)),
            'to_time' => date("h:i a", strtotime($this->to_time)),
        ];
    }
}
