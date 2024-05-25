<?php

namespace App\Models\Helpers;

use App\Models\Cuisine;
use App\Models\Catering;
use App\Models\CateringCuisine;
use App\Models\CateringMedia;

use App\Models\BusinessMedia;

trait CateringHelpers
{
    /*public function cuisines($id, $type = '') {
      $cuisines = CateringCuisine::where('catering_id', $id)->pluck('cuisine_id')->toArray();
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

    public function get_banners($business_id, $type = ''){
      $media = BusinessMedia::where(['business_id' => $business_id, 'media_type' => 'banner'])->get();
      if($type == 'single') {
        return ($media[0]) ? $media[0] : '';
      }
      return $media;
    }

    public function trim_all( $str , $what = NULL , $with = ' ' )
      {
          if( $what === NULL )
          {
              //  Character      Decimal      Use
              //  "\0"            0           Null Character
              //  "\t"            9           Tab
              //  "\n"           10           New line
              //  "\x0B"         11           Vertical Tab
              //  "\r"           13           New Line in Mac
              //  " "            32           Space
             
              $what   = "\\x00-\\x20";    //all white-spaces and control chars
          }
         
          return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
      }

    

}
