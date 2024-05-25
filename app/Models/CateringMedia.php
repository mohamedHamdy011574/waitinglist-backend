<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CateringMedia extends Model
{
    protected $table = 'catering_media';

    protected $fillable = ['catering_id', 'media_type', 'media_path'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
