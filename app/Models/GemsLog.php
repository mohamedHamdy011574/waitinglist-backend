<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GemsLog extends Model
{
    protected $table = 'gems_logs';
    protected $fillable = [
  		'sponsor_id','advertisement_id','type','customer_id','earned_gems'
    ];

    public function customer(){
    	return $this->hasOne(User::class, 'id','customer_id');
    }
   	
   	public function sponsor(){
    	return $this->hasOne(Sponsor::class, 'id','sponsor_id');
    }

    public function advertisement(){
      return $this->hasOne(Advertisement::class, 'id','advertisement_id');
    }
}
