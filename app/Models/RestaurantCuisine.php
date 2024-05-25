<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RestaurantCuisine extends Model
{
    protected $table = 'restaurant_cuisine';

    protected $fillable = ['restaurant_id', 'cuisine_id'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
