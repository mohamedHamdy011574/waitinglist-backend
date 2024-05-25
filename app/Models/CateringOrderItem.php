<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantMenu;
use App\Models\CateringOrder;

class CateringOrderItem extends Model
{
    protected $table = 'catering_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'catering_order_id','cat_packg_id', 'package_title', 'quantity', 'unit_price', 'addons_price',
    ];

    // function for filter records
    public function catering_package(){
        return $this->belongsTo(CateringPackage::class, 'cat_packg_id');
    }

    // function for filter records
    public function pickup_cart(){
        return $this->belongsTo(PickupOrder::class, 'pickup_order_id');
    }

     public function catering_addon_order() {
        return $this->hasOne(CateringAddonOrder::class, 'catering_order_item_id');
    }
}
