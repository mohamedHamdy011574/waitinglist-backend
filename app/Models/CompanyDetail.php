<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
	protected $table = 'company_detail';
    protected $fillable = [
  		'name','value'
    ];
    public static function get($name){
    	$value = Setting::where('name',$name)->first();
    	return $value->value; 
    }
}

