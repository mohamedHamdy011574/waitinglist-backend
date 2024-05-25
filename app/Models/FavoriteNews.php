<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteNews extends Model
{
    protected $table = 'favorite_news';
    protected $fillable = [
      'news_id', 'customer_id'
    ];
}
