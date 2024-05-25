<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\CuisineTranslation;
use Spatie\Permission\Models\Role;

class Cuisine extends Model
{
	use Translatable;

    protected $table = 'cuisines';

    protected $fillable = ['image', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name','description',];

    /**
     * @var string
     */
    public $translationForeignKey = 'cuisine_id';

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
    public $translationModel = CuisineTranslation::class;

    // function for filter records
    public function translation(){
    	return $this->hasMany(CuisineTranslation::class, 'cuisine_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
