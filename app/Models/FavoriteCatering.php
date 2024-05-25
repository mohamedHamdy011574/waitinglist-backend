<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteCatering extends Model
{
    protected $table = 'favorite_catering';
    protected $fillable = [
      'catering_id', 'customer_id'
    ];
}
