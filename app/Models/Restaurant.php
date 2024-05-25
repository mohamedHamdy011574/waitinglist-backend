<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\RestaurantTranslation;
use App\Models\Helpers\RestaurantHelpers;
use Spatie\Permission\Models\Role;

class Restaurant extends Model
{
  use Translatable;

    protected $table = 'restaurants';

    protected $fillable = ['link', 'status','working_status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name','description',];

    /**
     * @var string
     */
    public $translationForeignKey = 'restaurant_id';

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
    public $translationModel = RestaurantTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(RestaurantTranslation::class, 'restaurant_id','id');
    }

    // roles
    public function role(){
        return $this->belongsTo(Role::class);
    }

    // branches
    public function branches(){
      return $this->hasMany(RestaurantBranch::class, 'restaurant_id','id');
    }

    // working_hours
    public function working_hours(){
      return $this->hasOne(RestaurantWorkingHour::class, 'restaurant_id','id');
    }
}
