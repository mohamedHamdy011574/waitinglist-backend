<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class BusinessMedia extends Model
{
    protected $table = 'business_media';

    protected $fillable = ['business_id', 'media_type', 'media_path'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
