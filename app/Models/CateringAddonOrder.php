<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coupon;
use App\Models\User;
use App\Models\BusinessBranch;

class CateringAddonOrder extends Model
{
    protected $table = 'catering_addon_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'catering_order_item_id','item_count', 'quantity', 'total',
    ];

    // function for filter records
    public function catering_addon_order() {
        return $this->hasOne(CateringAddonOrder::class, 'catering_order_item_id');
    }


    public function catering_addon_order_items(){
        return $this->hasMany(CateringAddonOrderItem::class, 'catering_addon_order_id','id');
    }
}
