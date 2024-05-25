<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteBusiness extends Model
{
    protected $table = 'favorite_businesses';
    protected $fillable = [
       'customer_id', 'business_id', 'type'
    ];
}
