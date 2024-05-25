<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class BusinessBranchStaff extends Model
{
    protected $table = 'business_branch_staff';

    protected $fillable = ['business_branch_id', 'staff_id', 'manage_reservations', 'manage_waiting_list', 'manage_pickups', 'manage_catering_bookings'];


    // function for filter records
    public function business_branch(){
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }
 
}
