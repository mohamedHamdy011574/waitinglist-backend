<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Business;
use App\Models\User;
use App\Models\BusinessBranch;

class Reservation extends Model
{
    protected $table = 'reservations';


    const CONFIRMED_STATUS = 'confirmed';
    const CANCELLED_STATUS = 'cancelled';
    const CHECKED_IN_STATUS = 'checked_in';
    const CHECKED_OUT_STATUS = 'checked_out';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id','first_name','phone_number','business_id','business_branch_id', 'coupon_id', 'reserved_chairs', 'check_in_date', 'due_date', 'due_time', 'end_time', 'status', 'payment_status'
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

    // translation
    public function seating_areas(){
      return $this->hasMany(ReservationSeatingArea::class, 'reservation_id','id');
    }
}
