<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Business;
use App\Models\User;
use App\Models\BusinessBranch;

class WaitingList extends Model
{
    protected $table = 'waiting_list';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id','first_name','phone_number', 'token_number', 'business_id','business_branch_id', 'reserved_chairs',  'wl_datetime', 'wl_date', 'wl_time', 'status',
    ];

    // function for filter records
    public function restaurant(){
        return $this->belongsTo(Business::class, 'business_id');
    }

    // function for filter records
    public function customer(){
        return $this->belongsTo(User::class, 'customer_id');
    }

    // function for filter records
    public function restaurant_branch(){
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }

     // function for filter records
    public function branch_staff(){
        return $this->belongsTo(BusinessBranchStaff::class, 'business_branch_id');
    }
}
