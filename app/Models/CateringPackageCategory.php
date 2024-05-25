<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\CateringPacakgeCategoriesTranslation;
use Spatie\Permission\Models\Role;

class CateringPackageCategory  extends Model
{
  use Translatable;

    protected $table = 'catering_package_categories';

    protected $fillable = ['business_id','status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name'];

    /**
     * @var string
     */
    public $translationForeignKey = 'catering_pkg_cat_id';

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
    public $translationModel = CateringPacakgeCategoriesTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(CateringPacakgeCategoriesTranslation::class, 'catering_pkg_cat_id','id');
    }

    // catering package categories
    public function catering_packages(){
      return $this->hasMany(CateringPackage::class, 'catering_pkg_cat_id','id')->where('status','active');
    }


    
}
