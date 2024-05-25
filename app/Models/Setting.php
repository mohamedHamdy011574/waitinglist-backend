<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
  		'name','value','status'
    ];
    public static function get($name){
    	$value = Setting::where('name',$name)->first();
    	return $value->value; 
    }
}
