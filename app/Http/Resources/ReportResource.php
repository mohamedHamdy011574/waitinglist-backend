<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'reported_by' => $this->customer->first_name.' '.$this->customer->last_name,
            'concern' => $this->concern->concern,
            'comment' => $this->comment,
            'reported_date' => date('jS M Y',strtotime($this->created_at)),
            'reported_time' => date('H:i A',strtotime($this->created_at))
        ];
    }
}
