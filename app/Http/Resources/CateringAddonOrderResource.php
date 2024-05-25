<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class CateringAddonOrderResource extends JsonResource
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
            'addon_cart_items' => CateringAddonItemResource::collection($this->catering_addon_order_items),
            'total' => number_format($this->total,3, '.', ''),
            'cart_item_count' => $this->item_count,
        ];
    }
}
