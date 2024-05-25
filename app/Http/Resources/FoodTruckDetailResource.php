<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Helpers\FoodTruckHelpers;
use App\Models\FavoriteFoodTruck;
use Auth;
use Carbon\Carbon;

class FoodTruckDetailResource extends JsonResource
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

      //branches
      $branches = [];
      foreach($this->food_truck_branches as $r_b) {
        if($r_b->state_id == $this->selected_city_id) {
          $selected = true;
        }else{
          $selected = false;
        }
        array_push($branches, [
          'id' => $r_b->id,
          'business_id' => $r_b->business_id,
          'name' => $r_b->branch_name,
          'address' => $r_b->address,
          'latitude' => ($r_b->latitude) ? $r_b->latitude : '',
          'longitude' => ($r_b->longitude) ? $r_b->longitude : '',
          'city' => $r_b->state_id,
          'selected' => $selected,
        ]);
      }
      // $branches = $this->branches;



      
      //current status
      $current_status = 'available';



      return [
          'id' => $this->id,
          'name' => $this->brand_name,
          'description' => $this->description,
          'link' => $this->link,
          'link' => $this->link,
          'status' => $this->status,
          'banners' => MediaResource::collection($this->get_food_truck_banners($this->id)),
          'is_fav' => $is_fav,
          'branches' => $branches,
          'timings' => New WorkingHourResource($this->working_hours),
          'current_status' => $current_status,
      ];
    }
}
