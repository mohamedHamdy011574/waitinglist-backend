<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodTruckWorkingHour extends Model
{
    protected $table = "food_truck_working_hours";
    protected $fillable = ['food_truck_id', 'from_day', 'to_day', 'from_time', 'to_time'];
}