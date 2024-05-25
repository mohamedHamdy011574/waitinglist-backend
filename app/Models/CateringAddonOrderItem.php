<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantMenu;
use App\Models\CateringCart;

class CateringAddonOrderItem extends Model
{
    protected $table = 'catering_addon_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'catering_addon_order_id','cat_addon_id', 'quantity', 'unit_price',
    ];


    // function for filter records
    public function catering_addon(){
        return $this->belongsTo(CateringAddon::class, 'cat_addon_id');
    }



    // function for filter records
    // public function catering_addon_order() {
    //     return $this->hasOne(CateringAddonOrder::class, 'catering_order_item_id');
    // }

    // public function catering_addon_orders(){
    //     return $this->hasMany(CateringAddonOrder::class, 'catering_order_item_id','id');
    // }

    // public function catering_addon_order_items(){
    //     return $this->hasMany(CateringAddonOrder::class, 'catering_order_item_id','id');
    // }
}
