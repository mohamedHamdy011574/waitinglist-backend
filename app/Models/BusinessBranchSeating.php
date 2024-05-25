<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class BusinessBranchSeating extends Model
{
    protected $table = 'business_branch_seating';

    protected $fillable = ['business_branch_id', 'stg_area_id'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
