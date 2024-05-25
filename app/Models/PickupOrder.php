<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantMenu;
use App\Models\PickupCart;

class PickupOrder extends Model
{
    protected $table = 'pickup_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'first_name','phone_number','business_branch_id', 'coupon_id', 'item_count', 'quantity', 'sub_total', 'discount', 'taxes', 'grand_total', 'pickup_date', 'due_date', 'due_time', 'end_time', 'order_status', 'payment_status', 'payment_mode'
    ];    

    // function for filter records
    public function customer(){
        return $this->belongsTo(User::class, 'customer_id');
    }

    // function for filter records
    public function restaurant_branch(){
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }

    // function for filter records
    public function branch_staff(){
        return $this->belongsTo(BusinessBranchStaff::class, 'business_branch_id');
    }

    // function for filter records
    public function coupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function pickup_order_items(){
        return $this->hasMany(PickupOrderItem::class, 'pickup_order_id','id');
    }
}
