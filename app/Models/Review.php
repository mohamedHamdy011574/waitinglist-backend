<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $fillable = [
  		'given_by','blog_id','review'
    ];

    public function customer(){
    	return $this->hasOne(User::class, 'id','given_by');
    }
   	
   	public function food_blog(){
    	return $this->hasOne(FoodBlog::class, 'id','blog_id');
    }

    public function reports(){
    	return $this->hasMany(Report::class, 'review_id','id');
    }

    public function scopeHasReports($query)
    {
    	return $query->whereHas('reports');
    }
}
