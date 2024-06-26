<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\FoodTruckMenuTranslation;
use Spatie\Permission\Models\Role;

class FoodTruckMenu extends Model
{
  use Translatable;

    protected $table = 'food_truck_menus';

    protected $fillable = ['business_id', 'menu_category_id','menu_item_photo','price','currency','status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name','description'];

    /**
     * @var string
     */
    public $translationForeignKey = 'food_truck_menu_id';

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
    public $translationModel = FoodTruckMenuTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(FoodTruckMenuTranslation::class, 'food_truck_menu_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }

    // function for filter records
    public function menu_category(){
      return $this->hasOne(MenuCategory::class,'id' ,'menu_category_id');
    }

    // public function business(){
    //     return $this->belongsTo(Business::class, 'business_id');
    // }

    
}
