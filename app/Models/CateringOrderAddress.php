<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coupon;
use App\Models\User;
use App\Models\BusinessBranch;

class CateringOrderAddress extends Model
{
    protected $table = 'catering_order_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id','order_id', 'city_id', 'block', 'street', 'avenue', 'house_bulding', 'floor', 'apartment_number', 'mobile_number'
    ];

    public function city(){
        return $this->belongsTo(State::class, 'city_id' );
    }
}
