<?php

namespace App\Models\Helpers;

trait PickupHelpers
{

  public function splitTime($StartTime, $EndTime, $Duration="15") {
    $ReturnArray = array();// Define output

    $StartTime    = strtotime($StartTime); //Get Timestamp
    $EndTime      = strtotime($EndTime); //Get Timestamp

    $startDate = date('Y-m-d', $StartTime);
    // CHEKING IS TODAY THEN HIDE PAST HOURS AND GET MINUTES IN MULTIPLY OF FIVE
    if(date('Y-m-d', $StartTime) == date('Y-m-d')) {
        if($StartTime < time()){
            $minute = date('i',time());
            // echo "<pre>";print_r(($Duration));exit;
            if(($minute % $Duration) > 0) // 7 - 5 = 2 > 0
            {
                $fv_mnt_mlt = ($minute % $Duration);
                $StartTime = time() - ($fv_mnt_mlt * 60) - date('s',time());
            }else{
                $StartTime = time() - date('s',time());
            }
        }
    }
    // CHEKING IS TODAY THEN HIDE PAST HOURS

    $AddMins  = $Duration * 60;
    // echo "<pre>";print_r(date('H:i:s',$StartTime));exit;
    // echo "<pre>";print_r(date('H:i:s',$Endtime));exit;
    // echo "<pre>";print_r($StartTime);exit;
    while ($StartTime <= $EndTime) //Run loop
    {
      if($startDate == date('Y-m-d')) {
      // echo "<pre>";print_r("this");exit;
        if($StartTime > time())
        {
          $ReturnArray[date("H:i:s", $StartTime)] = date("h:i A", $StartTime);
        }
      }
      else
      {
        $ReturnArray[date("H:i:s", $StartTime)] = date("h:i A", $StartTime);
      }
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

      // CHEKING IS TODAY THEN HIDE PAST HOURS AND GET MINUTES IN MULTIPLY OF SIXTY
      if(date('Y-m-d', $StartTime) == date('Y-m-d')){
          if($StartTime < time()){
              $minute = date('i',time());
              if(($minute % $Duration) > 0) // 7 - 5 = 2 > 0
              {
                $fv_mnt_mlt = ($minute % $Duration);
                $StartTime = time() - ($fv_mnt_mlt * 60) - date('s',time());
              } else {
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

}
