<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class BusinessCuisine extends Model
{
    protected $table = 'business_cuisine';

    protected $fillable = ['business_id', 'cuisine_id'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
