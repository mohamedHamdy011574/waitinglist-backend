<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
  		'customer_id','logo', 'video', 'first_name', 'last_name', 'country_code', 'contact_number' ,'email' ,'duration_from', 'duration_to', 'notified_at', 'ad_price', 'payment_mode', 'payment_status', 'status',
  		];
}
