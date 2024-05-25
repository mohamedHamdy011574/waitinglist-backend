<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CateringCuisine extends Model
{
    protected $table = 'catering_cuisine';

    protected $fillable = ['catering_id', 'cuisine_id'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
