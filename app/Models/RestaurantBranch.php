<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\RestaurantBranchTranslation;
use Spatie\Permission\Models\Role;
use App\Models\Restaurant;

class RestaurantBranch extends Model
{
  use Translatable;

    protected $table = 'rest_branches';

    protected $fillable = ['restaurant_id','manager_id','state_id','total_seats','available_seats','latitude', 'longitude', 'is_master', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name','address',];

    /**
     * @var string
     */
    public $translationForeignKey = 'rest_branch_id';

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
    public $translationModel = RestaurantBranchTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(RestaurantBranchTranslation::class, 'rest_branch_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }

    // function for filter records
    public function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    
}
