<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\CateringPlanTranslation;
use Spatie\Permission\Models\Role;

class CateringPlan extends Model
{
  use Translatable;

    protected $table = 'catering_plans';

    protected $fillable = ['business_id','persons_served_min','persons_served_max','sunday_serving','monday_serving','tuesday_serving','wednesday_serving','thursday_serving','friday_serving','saturday_serving','from_time','to_time','plan_rate','currency', 'served_in_restaurant', 'served_off_premises','setup_time','setup_time_unit','max_time','max_time_unit','status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['plan_name','description','food_serving'];

    /**
     * @var string
     */
    public $translationForeignKey = 'catering_plan_id';

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
    public $translationModel = CateringPlanTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(CateringPlanTranslation::class, 'catering_plan_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function business(){
        return $this->belongsTo(Business::class, 'business_id');
    }

    
}
