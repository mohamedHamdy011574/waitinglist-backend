<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Helpers\FoodTruckHelpers;
use App\Models\FavoriteFoodTruck;
use Auth;
use Carbon\Carbon;

class FoodTruckResource extends JsonResource
{
    use FoodTruckHelpers; 
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $user = Auth::guard('api')->user();
      
      //is favorite?
      $fav = FavoriteFoodTruck::where(['customer_id' => @$user->id, 'food_truck_id' => $this->id])->get()->count();
      $is_fav = false;
      if($fav) {
        $is_fav = true;
      }

      return [
          'id' => $this->id,
          'name' => $this->brand_name,
          'description' => $this->description,
          'link' => $this->link,
          'status' => $this->status,
          'banner' => New MediaResource($this->get_food_truck_banners($this->id, 'single')),
          'is_fav' => $is_fav,
          'timings' => New WorkingHourResource($this->working_hours),
      ];
    }
}
