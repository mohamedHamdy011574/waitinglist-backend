<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    protected $table = 'wallet_logs';
    protected $fillable = [
  		'customer_id','amount', 'type'
    ];

    public function customer(){
    	return $this->hasOne(User::class, 'id','customer_id');
    }
   	
}
