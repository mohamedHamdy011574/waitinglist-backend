<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessWorkingHour extends Model
{
    protected $table = "business_working_hours";
    protected $fillable = ['business_id','sunday_serving','monday_serving','tuesday_serving','wednesday_serving','thursday_serving','friday_serving','saturday_serving', 'from_day', 'to_day', 'from_time', 'to_time'];
}