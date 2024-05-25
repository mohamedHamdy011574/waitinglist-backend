<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\FoodTruckBranchTranslation;
use Spatie\Permission\Models\Role;
use App\Models\FoodTruck;

class FoodTruckBranch extends Model
{
  use Translatable;

    protected $table = 'ftruck_branches';

    protected $fillable = ['food_truck_id','manager_id','state_id','latitude', 'longitude', 'is_master', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name','address',];

    /**
     * @var string
     */
    public $translationForeignKey = 'ftruck_branch_id';

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
    public $translationModel = FoodTruckBranchTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(FoodTruckBranchTranslation::class, 'ftruck_branch_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }

    // function for filter records
    public function food_truck(){
        return $this->belongsTo(FoodTruck::class, 'food_truck_id');
    }

    
}
