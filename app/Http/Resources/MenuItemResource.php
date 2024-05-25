<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class MenuItemResource extends JsonResource
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
            'name' => ucfirst($this->name),
            'description' => $this->description,
            'photo' => url($this->menu_item_photo),
            // 'price' => $this->currency.' '.$this->price,
            'price' => $this->currency.' '.number_format((float)$this->price, 3, '.', ''),
            'is_selected' => false, // static
            'count' => 0, // static
        ];
    }
}
