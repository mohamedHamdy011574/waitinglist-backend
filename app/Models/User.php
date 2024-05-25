<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
// use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
// use Spatie\MediaLibrary\HasMedia\HasMedia;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, HasRoles;
     // HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone_number', 'user_type','vendor_id',  'profile_pic', 'birth_date', 'country_id', 'city_id', 'gems', 'e_wallet_amount', 'status', 'verified', 'password','preferred_language',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // device Details
    public function device_detail()
    {
        return $this->hasMany('App\Models\DeviceDetail','user_id');
    }

    // restaurant
    public function restaurant()
    {
      return $this->hasOne('App\Models\Restaurant','manager_id','id');
    }

    // restaurant branch
    public function restaurant_branch()
    {
      return $this->hasOne('App\Models\RestaurantBranch','manager_id','id');
    }

    // blogger
    public function blogger()
    {
      return $this->hasOne('App\Models\Blogger','customer_id','id');
    }

    // reservations
    public function reservations()
    {
        return $this->hasMany('App\Models\Reservation','customer_id')->paginate();
    }

    // subscriptions
    public function subscription()
    {
        return $this->hasOne('App\Models\Subscription','vendor_id');
    }

    // business
    public function business()
    {
      return $this->hasOne('App\Models\Business','vendor_id','id');
    }

    public function business_branch_staff() {
        return $this->hasOne('App\Models\BusinessBranchStaff','staff_id');
    }
}
