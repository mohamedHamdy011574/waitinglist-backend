<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteFoodTruck extends Model
{
    protected $table = 'favorite_food_trucks';
    protected $fillable = [
      'food_truck_id', 'customer_id'
    ];
}
