<?php

namespace App\Models\Helpers;

use App\Models\Restaurant;
use App\Models\RestaurantCuisine;
use App\Models\RestaurantMedia;
use App\Models\Cuisine;

use App\Models\BusinessMedia;

trait RestaurantHelpers
{

    /*public function cuisines($id, $type = '') {
      $cuisines = RestaurantCuisine::where('restaurant_id', $id)->pluck('cuisine_id')->toArray();
      if($type == 'string'){
        $cuisinesString = '';
        foreach (Cuisine::whereIn('id', $cuisines)->get() as $csn) {
          $cuisinesString .= $csn->name. ', ';
        }
        return rtrim($cuisinesString, ', ');
      } else {
        Cuisine::whereIn('id', $cuisines)->get();
      }
    }

    public function restaurants_have_cuisines($cuisines) {
      $cuisines_array = [];  
      if($cuisines && $cuisines != '') {
        $cuisines_array = explode(',', rtrim(trim($cuisines),','));
        $restaurants = RestaurantCuisine::whereIn('cuisine_id', $cuisines_array)->pluck('restaurant_id')->toArray();
        return $restaurants;
      } else {
        return [];
      }
    }*/

    public function get_restant_banners($business_id, $type = '') {
      $media = BusinessMedia::where(['business_id' => $business_id, 'media_type' => 'banner'])->get();
      if($type == 'single') {
        return ($media[0]) ? $media[0] : '';
      }
      return $media;
    }

    // function to get working days array from from-day and to-day
    public function get_working_days_old($from, $to) {
      $days = [];
      foreach([0,1,2,3,4,5,6] as $d) {
        if($d == $from) {
          array_push($days, $d);    
        } else { 
          if($from < $to) {
            if($d >= $from && $d <= $to){
              array_push($days, $d);
            }
          } else {
            if($d >= $from && $d <= 6){
              array_push($days, $d);
            }
            if($d <= $to){
              array_push($days, $d);
            }
          }
        }
      }
      return $days;
    }

    public function get_working_days($working_days) {
      $days = [];
      if($working_days->monday_serving)
      {
        array_push($days, 1);
      }
      if($working_days->tuesday_serving)
      {
        array_push($days, 2);
      }
      if($working_days->wednesday_serving)
      {
        array_push($days, 3);
      }
      if($working_days->thursday_serving)
      {
        array_push($days, 4);
      }
      if($working_days->friday_serving)
      {
        array_push($days, 5);
      }
      if($working_days->saturday_serving)
      {
        array_push($days, 6);
      }
      if($working_days->sunday_serving)
      {
        array_push($days, 7);
      }
      return $days;
    }
    

}
