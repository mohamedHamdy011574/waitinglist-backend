<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RestaurantMedia extends Model
{
    protected $table = 'restaurant_media';

    protected $fillable = ['restaurant_id', 'media_type', 'media_path'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
