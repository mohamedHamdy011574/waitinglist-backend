<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantMenu;
use App\Models\PickupOrder;

class PickupOrderItem extends Model
{
    protected $table = 'pickup_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pickup_order_id','menu_id', 'menu_title', 'quantity', 'unit_price'
    ];

    // function for filter records
    public function menu(){
        return $this->belongsTo(RestaurantMenu::class, 'menu_id');
    }

    // function for filter records
    public function pickup_cart(){
        return $this->belongsTo(PickupOrder::class, 'pickup_order_id');
    }
}
