<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantMenu;
use App\Models\CateringCart;

class CateringCartItem extends Model
{
    protected $table = 'catering_cart_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'catering_cart_id','cat_packg_id', 'package_title', 'quantity', 'unit_price','addons_price', 'special_request',
    ];

    // function for filter records
    public function catering_package(){
        return $this->belongsTo(CateringPackage::class, 'cat_packg_id');
    }

    // function for filter records
    public function catering_cart() {
        return $this->belongsTo(CateringCart::class, 'catering_cart_id');
    }

    public function catering_addon_cart(){
        return $this->hasOne(CateringAddonCart::class, 'catering_cart_item_id','id');
    }
}
