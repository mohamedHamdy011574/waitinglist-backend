<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class BusinessCoupon extends Model
{
    protected $table = 'business_coupon';

    protected $fillable = ['business_id', 'coupon_id'];
}
