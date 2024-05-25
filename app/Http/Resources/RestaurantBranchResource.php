<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class RestaurantBranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $user = Auth::guard('api')->user();
      if($user) {

      }
      $selected = true;

      return [
        'id' => $this->id,
        'business_id' => $this->business_id,
        'name' => $this->branch_name,
        'state' => $this->state_id,
        'selected' => $selected,
      ];
    }
}
