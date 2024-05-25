<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RestaurantCoupon extends Model
{
    protected $table = 'restaurant_coupon';

    protected $fillable = ['restaurant_id', 'coupon_id'];
}
