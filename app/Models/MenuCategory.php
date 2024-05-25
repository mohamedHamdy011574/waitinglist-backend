<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\MenuCategoryTranslation;
use Spatie\Permission\Models\Role;

class MenuCategory extends Model
{
	use Translatable;

    protected $table = 'menu_categories';

    protected $fillable = ['status','business_id'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name'];

    /**
     * @var string
     */
    public $translationForeignKey = 'menu_category_id';

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
    public $translationModel = MenuCategoryTranslation::class;

    // function for filter records
    public function translation(){
    	return $this->hasMany(MenuCategoryTranslation::class, 'menu_category_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
