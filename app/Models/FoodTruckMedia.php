<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class FoodTruckMedia extends Model
{
    protected $table = 'food_truck_media';

    protected $fillable = ['food_truck_id', 'media_type', 'media_path'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
