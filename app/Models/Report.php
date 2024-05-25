<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $fillable = [
  		'review_id','reported_by','concern_id','comment'
    ];

    public function customer(){
    	return $this->hasOne(User::class, 'id','reported_by');
    }

    public function reviews(){
    	return $this->hasMany(Review::class, 'id','review_id');
    }
   	
   	public function concern(){
    	return $this->hasOne(Concern::class, 'id','concern_id');
    }
}
