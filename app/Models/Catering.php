<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\CateringTranslation;
use Spatie\Permission\Models\Role;

class Catering extends Model
{
  use Translatable;

    protected $table = 'catering';

    protected $fillable = [ 'price', 'link', 'state_id', 'latitude', 'longitude', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name','description', 'food_serving', 'address'];

    /**
     * @var string
     */
    public $translationForeignKey = 'catering_id';

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
    public $translationModel = CateringTranslation::class;

    // translation
    public function translation() {
      return $this->hasMany(CateringTranslation::class, 'catering_id','id');
    }

    // roles
    public function role() {
        return $this->belongsTo(Role::class);
    }

    // working_hours
    public function working_hours() {
      return $this->hasOne(CateringWorkingHour::class, 'catering_id','id');
    }

    // function for filter records
    public function restaurant_branch(){
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }
}
