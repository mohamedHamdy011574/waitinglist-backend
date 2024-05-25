<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CateringPackageMedia extends Model
{
    protected $table = 'catering_package_media';

    protected $fillable = ['catering_pkg_id','image'];
}
