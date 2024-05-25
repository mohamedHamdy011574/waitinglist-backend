<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringWorkingHour extends Model
{
    protected $table = "catering_working_hours";
    protected $fillable = ['catering_id', 'from_day', 'to_day', 'from_time', 'to_time'];
}