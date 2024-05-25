<?php

namespace App\Models\Helpers;

use App\Models\Restaurant;
use App\Models\RestaurantCuisine;
use App\Models\RestaurantMedia;
use App\Models\Cuisine;

trait ReservationHelpers
{

  public function splitTime($StartTime, $EndTime, $Duration="15") {
    $ReturnArray = array();// Define output

    // echo '<br>'.$StartTime; 
    // echo '<br>'.$EndTime; 
    $StartTime    = strtotime($StartTime); //Get Timestamp
    $EndTime      = strtotime($EndTime); //Get Timestamp

    // CHEKING IS TODAY THEN HIDE PAST HOURS AND GET MINUTES IN MULTIPLY OF FIVE
    if(date('Y-m-d', $StartTime) == date('Y-m-d')) {
        if($StartTime < time()){
            $minute = date('i',time());
            if(($minute % $Duration) > 0) // 7 - 5 = 2 > 0
            {
                $fv_mnt_mlt = ($Duration - ($minute % $Duration));
                $StartTime = time() + ($fv_mnt_mlt * 60) - date('s',time());
            }else{
                $StartTime = time() - date('s',time());
            }
        }
    }
    // CHEKING IS TODAY THEN HIDE PAST HOURS

    $AddMins  = $Duration * 60;

    while ($StartTime <= $EndTime) //Run loop
    {
        $ReturnArray[date("H:i:s", $StartTime)] = date("h:i A", $StartTime);
        $StartTime += $AddMins; //Endtime check
    }
    return $ReturnArray;
  }

  public function splitTimeRezerved($StartTime, $EndTime, $Duration="15") {
      $ReturnArray = array();// Define output

      // echo '<br>'.$StartTime; 
      // echo '<br>'.$EndTime; 
      $StartTime    = strtotime($StartTime); //Get Timestamp
      $EndTime      = strtotime($EndTime); //Get Timestamp

      // CHEKING IS TODAY THEN HIDE PAST HOURS AND GET MINUTES IN MULTIPLY OF FIVE
      if(date('Y-m-d', $StartTime) == date('Y-m-d')){
          if($StartTime < time()){
              $minute = date('i',time());
              if(($minute % $Duration) > 0) // 7 - 5 = 2 > 0
              {
                  $fv_mnt_mlt = ($Duration - ($minute % $Duration));
                  $StartTime = time() + ($fv_mnt_mlt * 60) - date('s',time());
              }else{
                  $StartTime = time() - date('s',time());
              }
          }
      }
      // CHEKING IS TODAY THEN HIDE PAST HOURS

      $AddMins  = $Duration * 60;

      while ($StartTime <= $EndTime) //Run loop
      {
          $ReturnArray[date("H:i:s", $StartTime)] = date("h:i A", $StartTime);
          $StartTime += $AddMins; //Endtime check
      }

      array_pop($ReturnArray);
      return $ReturnArray;
  }

  public function splitTimeCore($StartTime, $EndTime, $Duration="15", $serviceTime)
    {
        $ReturnArray = array();// Define output
        /*echo '<br><b>=====starts=====</b>';
        echo '<br>StartTime - '.$StartTime; 
        echo '<br>EndTime - '.$EndTime; */
        $StartTime    = strtotime($StartTime); //Get Timestamp
        $EndTime      = strtotime($EndTime); //Get Timestamp

        $timeDiffinM = ($EndTime - $StartTime)/60;
        // echo '<br>timeDiffinM - '.$timeDiffinM;
        $AddMins  = $Duration * 60;
        if($timeDiffinM >= $serviceTime){ //80 < 60
            // echo '<br>service nani<br>';
            $StartTime = strtotime("+".($timeDiffinM-$serviceTime)." minutes",$StartTime); //SERVICE TIME EARLY
            
            while ($StartTime <= $EndTime) //Run loop
            {   
                $differnce = ($EndTime - $StartTime)/60;
                /*echo "<br>differnce -".$differnce;
                echo "<br>servcietime -".$serviceTime;
                echo "<br>StartTime -".date("H:i:s", $StartTime);
                echo "<br>EndTime -".date("H:i:s", $EndTime);*/
                if($differnce != $serviceTime){
                    $ReturnArray[date("H:i:s", $StartTime)] = date("h:i A", $StartTime);
                }
                $StartTime += $AddMins; //Endtime check
            }
        }
        else{
             //echo 'service moti<br>';
            // $EndTime = $EndTime - ($serviceTime * 60); //SERVICE TIME EARLY
            while ($StartTime <= $EndTime) //Run loop
            {
                $ReturnArray[date("H:i:s", $StartTime)] = date("h:i A", $StartTime);
                $StartTime += $AddMins; //Endtime check
            }
        }
        array_pop($ReturnArray);
        return $ReturnArray;
    }

    

}
