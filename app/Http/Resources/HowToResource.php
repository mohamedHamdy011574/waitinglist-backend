<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HowToResource extends JsonResource
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
            'question' => ucfirst($this->question),
            'answer'    => $this->answer,
            'display_order'  =>  $this->display_order,
        ];

    }
}
