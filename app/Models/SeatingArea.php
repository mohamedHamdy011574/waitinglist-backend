<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\SeatingAreaTranslation;
use Spatie\Permission\Models\Role;

class SeatingArea extends Model
{
	use Translatable;

    protected $table = 'seating_area';

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
    public $translationForeignKey = 'stg_area_id';

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
    public $translationModel = SeatingAreaTranslation::class;

    // function for filter records
    public function translation(){
    	return $this->hasMany(SeatingAreaTranslation::class, 'stg_area_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
