<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantWorkingHour extends Model
{
    protected $table = "restaurant_working_hours";
    protected $fillable = ['restaurant_id', 'from_day', 'to_day', 'from_time', 'to_time'];
}