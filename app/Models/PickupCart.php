<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coupon;
use App\Models\User;
use App\Models\BusinessBranch;

class PickupCart extends Model
{
    protected $table = 'pickup_carts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id','business_branch_id', 'coupon_id', 'item_count', 'quantity', 'sub_total', 'discount', 'taxes', 'grand_total'
    ];

    // grand total of the cart
    public function grand_total()
    {
        return ($this->sub_total + $this->taxes) - $this->discount;
    }

    // function for filter records
    public function customer(){
        return $this->belongsTo(User::class, 'customer_id');
    }

    // function for filter records
    public function restaurant_branch(){
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }

    // function for filter records
    public function coupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function pickup_cart_items(){
        return $this->hasMany(PickupCartItem::class, 'pickup_cart_id','id');
    }
}
