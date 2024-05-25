<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RestaurantBranchSeating extends Model
{
    protected $table = 'restaurant_branch_seating';

    protected $fillable = ['rest_branch_id', 'stg_area_id'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
