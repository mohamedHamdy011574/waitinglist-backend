<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\HowToTranslation;
use Spatie\Permission\Models\Role;

class HowTo extends Model
{
	use Translatable;

    protected $table = 'how_to';

    protected $fillable = ['display_order', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['question','answer',];

    /**
     * @var string
     */
    public $translationForeignKey = 'how_to_id';

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
    public $translationModel = HowToTranslation::class;

    // function for filter records
    public function translation(){
    	return $this->hasMany(HowToTranslation::class, 'how_to_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
