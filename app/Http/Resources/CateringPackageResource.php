<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;
use App\Models\CateringPackageMedia;
use App\Models\CateringAddon;
use App\Models\BusinessBranch;
use Auth;
use Carbon\Carbon;

class CateringPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
    
      $banners = CateringPackageMedia::where('catering_pkg_id', $this->id)->get();
      $banners_array = [];
      foreach ($banners as $bnr) {
        array_push($banners_array, [
            'id' => $bnr->id,
            'media_type' => 'banner',
            'media_path' => url($bnr->image),
        ]);
      }
      // if($banner) {
      //   $banner = url($banner->image);
      // }else{
      //   $banner = url('uploads/default.png');
      // }

      $addons = CateringAddon::where('business_id', $this->business_id)->get();
      if(count($addons)){
        $addons_data = CateringAddonResource::collection($addons);
      }else{
        $addons_data = [];
      }

      $business_branch = BusinessBranch::where(['business_id' => $this->business_id, 'branch_type' => 'catering'])->first();

      return [
          'id' => $this->id,
          'package_name' => $this->package_name,
          // 'business_id' => $this->business_id,
          'business_branch_id' => @$business_branch->id,
          'food_serving' => $this->food_serving,
          'banners' =>   $banners_array,
          'price' => Setting::get('currency').' '.number_format((float)$this->price, 3, '.', ''),
          'setup_time' => $this->setup_time.' '.$this->setup_time_unit,
          'max_time' => $this->max_time.' '.$this->max_time_unit,
          'addons' => $addons_data,
      ];
    }
}
