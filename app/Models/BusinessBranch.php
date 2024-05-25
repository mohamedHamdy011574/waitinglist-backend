<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\BusinessBranchTranslation;
use Spatie\Permission\Models\Role;
use App\Models\Restaurant;

class BusinessBranch extends Model
{
  use Translatable;

    protected $table = 'business_branches';

    protected $fillable = ['business_id','branch_type','branch_email','branch_phone_number','reservation_allow','waiting_list_allow','pickup_allow','cash_payment_allow','online_payment_allow','wallet_payment_allow','pickups_per_hour','min_notice_unit','min_notice','delivery_charge','min_order', 'address','state_id','latitude', 'longitude', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['branch_name'];

    /**
     * @var string
     */
    public $translationForeignKey = 'business_branch_id';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The class name for the localed model.
     *
     * @var string
     */
    public $translationModel = BusinessBranchTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(BusinessBranchTranslation::class, 'business_branch_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }

    // function for filter records
    public function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function business(){
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function state(){
        return $this->belongsTo(State::class, 'state_id' );
    }

    
}
