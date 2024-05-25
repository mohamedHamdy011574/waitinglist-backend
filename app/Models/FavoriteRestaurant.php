<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteRestaurant extends Model
{
    protected $table = 'favorite_restaurants';
    protected $fillable = [
      'restaurant_id', 'customer_id'
    ];
}
