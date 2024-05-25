<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coupon;
use App\Models\User;
use App\Models\BusinessBranch;

class CateringAddonCart extends Model
{
    protected $table = 'catering_addon_carts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'catering_cart_item_id','item_count', 'quantity', 'total',
    ];


    public function catering_addon_cart_items(){
        return $this->hasMany(CateringAddonCartItem::class, 'catering_addon_cart_id','id');
    }
}
