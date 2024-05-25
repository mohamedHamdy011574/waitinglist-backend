<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\CateringPacakgeTranslation;
use Spatie\Permission\Models\Role;

class CateringPackage  extends Model
{
  use Translatable;

    protected $table = 'catering_packages';

    protected $fillable = ['catering_pkg_cat_id','person_serve','price','setup_time','setup_time_unit','max_time','max_time_unit','status','business_id','currency'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['package_name','food_serving'];

    /**
     * @var string
     */
    public $translationForeignKey = 'catering_pkg_id';

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
    public $translationModel = CateringPacakgeTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(CateringPacakgeTranslation::class, 'catering_pkg_id','id');
    }
    
    // catering package category
    public function category(){
        return $this->belongsTo(CateringPackageCategory::class,'catering_pkg_cat_id');
    }

    // function for filter records
    public function photos(){
      return $this->hasMany(CateringPackageMedia::class, 'catering_pkg_id','id');
    }
}
