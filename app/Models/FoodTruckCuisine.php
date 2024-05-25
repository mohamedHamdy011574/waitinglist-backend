<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class FoodTruckCuisine extends Model
{
    protected $table = 'food_truck_cuisine';

    protected $fillable = ['food_truck_id', 'cuisine_id'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
