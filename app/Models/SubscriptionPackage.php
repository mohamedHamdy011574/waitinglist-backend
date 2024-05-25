<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\SubscriptionPackageTranslation;
use Spatie\Permission\Models\Role;

class SubscriptionPackage extends Model
{
  use Translatable;

    protected $table = 'subscription_packages';

    protected $fillable = ['for_restaurant', 'for_catering','for_food_truck','reservation', 'waiting_list', 'pickup', 'branches_include', 'subscription_period', 'package_price', 'currency', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['package_name','description',];

    /**
     * @var string
     */
    public $translationForeignKey = 'sub_package_id';

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
    public $translationModel = SubscriptionPackageTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(SubscriptionPackageTranslation::class, 'sub_package_id','id');
    }
}
