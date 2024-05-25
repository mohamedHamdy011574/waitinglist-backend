<?php

namespace App\Models\Helpers;

use App\Models\FoodTruck;
use App\Models\FoodTruckCuisine;
use App\Models\FoodTruckMedia;
use App\Models\Cuisine;

use App\Models\BusinessMedia;

trait FoodTruckHelpers
{

    /*public function cuisines($id, $type = '') {
      $cuisines = FoodTruckCuisine::where('food_truck_id', $id)->pluck('cuisine_id')->toArray();
      if($type == 'string'){
        $cuisinesString = '';
        foreach (Cuisine::whereIn('id', $cuisines)->get() as $csn) {
          $cuisinesString .= $csn->name. ', ';
        }
        return rtrim($cuisinesString, ', ');
      } else {
        Cuisine::whereIn('id', $cuisines)->get();
      }
    }*/


    public function get_food_truck_banners($business_id, $type = ''){
      $media = BusinessMedia::where(['business_id' => $business_id, 'media_type' => 'banner'])->get();
      if($type == 'single') {
        return ($media[0]) ? $media[0] : '';
      }
      return $media;
    }

    

}
