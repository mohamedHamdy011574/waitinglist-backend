<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\RestaurantBranch;

class ReservationSeatingArea extends Model
{
    protected $table = 'reservation_seating_area';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reservation_id','stg_area_id'
    ];
}
