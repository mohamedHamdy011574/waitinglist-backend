<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantMenu;
use App\Models\CateringCart;

class CateringAddonCartItem extends Model
{
    protected $table = 'catering_addon_cart_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'catering_addon_cart_id','cat_addon_id', 'quantity', 'unit_price',
    ];


    // function for filter records
    public function catering_addon(){
        return $this->belongsTo(CateringAddon::class, 'cat_addon_id');
    }



    // function for filter records
    public function catering_addon_cart() {
        return $this->belongsTo(CateringAddonCart::class, 'catering_addon_cart_id');
    }
}
