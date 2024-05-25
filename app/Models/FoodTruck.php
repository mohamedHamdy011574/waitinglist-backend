<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\FoodTruckTranslation;
use App\Models\Helpers\FoodTruckHelpers;
use Spatie\Permission\Models\Role;

class FoodTruck extends Model
{
  use Translatable;

    protected $table = 'food_trucks';

    protected $fillable = ['link', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name','description',];

    /**
     * @var string
     */
    public $translationForeignKey = 'food_truck_id';

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
    public $translationModel = FoodTruckTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(FoodTruckTranslation::class, 'food_truck_id','id');
    }

    // roles
    public function role(){
        return $this->belongsTo(Role::class);
    }

    // branches
    public function branches(){
      return $this->hasMany(FoodTruckBranch::class, 'food_truck_id','id');
    }

    // working_hours
    public function working_hours(){
      return $this->hasOne(FoodTruckWorkingHour::class, 'food_truck_id','id');
    }
}
