<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavouriteFoodBlog extends Model
{
    protected $table = 'favourite_food_blogs';
    protected $fillable = [
      'food_blog_id', 'customer_id',
    ];
}
