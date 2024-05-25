<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FavoriteBusiness;
use App\Models\Helpers\CateringHelpers;
use App\Models\Catering;
use App\Models\Setting;
use Auth;
use Carbon\Carbon;

class CateringResource extends JsonResource
{
  use CateringHelpers;
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
      $fav = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $this->id, 'type' => 'catering'])->get()->count();
      $is_fav = false;
      if($fav) {
        $is_fav = true;
      }

      return [
          'id' => $this->id,
          'name' => $this->brand_name,
          'description' => $this->description,
          // 'food_serving' => $this->food_serving,
          'food_serving' => str_replace(' ','',$this->trim_all($this->food_serving)),
          'link' => $this->link,
          'address' => $this->catering_branch->address,
          'price' => Setting::get('currency').' '.number_format((float)$this->price, 3, '.', ''),
          'status' => $this->status,
          'banner' => New MediaResource($this->get_banners($this->id, 'single')),
          'city' => $this->catering_branch->state_id,
          'all_banners' => MediaResource::collection($this->get_banners($this->id)),
          'is_fav' => $is_fav,
          'timings' => New WorkingHourResource($this->working_hours),
      ];
    }
}
