<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTiming extends Model
{
    protected $fillable = ['rest_branch_id', 'week_day', 'from', 'to', 'reservations_capacity'];
}
