<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blogger extends Model
{

    protected $fillable = [
  		'customer_id','blogger_name','blogger_photo',
  		];
}
