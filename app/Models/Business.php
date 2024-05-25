<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\BusinessTranslation;
use App\Models\Helpers\BusinessHelpers;
use Spatie\Permission\Models\Role;

class Business extends Model
{
  use Translatable;

    protected $table = 'businesses';

    protected $fillable = ['vendor_id', 'brand_email', 'brand_phone_number', 'link', 'brand_logo', 'reservation_status', 'waiting_list_status', 'pickup_status', 'working_status', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['brand_name','description',];

    /**
     * @var string
     */
    public $translationForeignKey = 'business_id';

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
    public $translationModel = BusinessTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(BusinessTranslation::class, 'business_id','id');
    }

    // roles
    public function role(){
        return $this->belongsTo(Role::class);
    }

    // // branches
    // public function resbranches(){
    //   return $this->hasMany(RestaurantBranch::class, 'restaurant_id','id');
    // }

    // restaurant_branches
    public function restaurant_branches(){
      return $this->hasMany(BusinessBranch::class, 'business_id','id')->where('branch_type','restaurant');
    }

    // catering_branch
    public function catering_branch(){
      return $this->hasOne(BusinessBranch::class, 'business_id','id')->where('branch_type','catering');
    }

    // food_truck_branches
    public function food_truck_branches(){
      return $this->hasMany(BusinessBranch::class, 'business_id','id')->where('branch_type','food_truck');
    }

    // working_hours
    public function working_hours(){
      return $this->hasOne(BusinessWorkingHour::class, 'business_id','id');
    }

    // translation
    public function restaurant_menu(){
      return $this->hasMany(RestaurantMenu::class, 'business_id','id');
    }

    // menus_categories
    public function menus_categories(){
      return $this->hasMany(MenuCategory::class, 'business_id','id');
    }

    // roles
    public function vendor(){
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // catering package categories
    public function catering_package_categories(){
      return $this->hasMany(CateringPackageCategory::class, 'business_id','id')->where('status','active');
    }

    // ratings_review
    public function catering_ratings_reviews(){
      return $this->hasMany(BusinessRatingReview::class, 'business_id','id')->where('branch_type','catering');
    }


    // reswrvation hours
    public function reservation_hours(){
      return $this->hasMany(ReservationHour::class, 'business_id','id');
    }




    // catering_plans
    /*public function catering_plans() {
      return $this->hasMany(CateringPlan::class, 'business_id','id');  
    }*/



}
