<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodBlog extends Model
{
    protected $table = 'food_blogs';
    protected $fillable = [
  		'added_by','recipe_name','description','recipe_video','recipe_audio','status','cuisine_id','recipe_image','is_link',
    ];


    public function cuisine()
    {
        return $this->hasOne(Cuisine::class, 'id' ,'cuisine_id');
    }

    public function blogger()
    {
        return $this->hasOne(Blogger::class, 'id', 'added_by');
    }

   
}
