<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CateringPlanAddon extends Model
{
    protected $table = 'catering_plan_addon';

    protected $fillable = ['catering_plan_id', 'catering_addon_id'];

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
