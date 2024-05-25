<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\ConcernTranslation;
use Spatie\Permission\Models\Role;

class Concern extends Model
{
	use Translatable;
    protected $table = 'concerns';
    protected $fillable = [
  		'status'
    ];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['concern'];

    /**
     * @var string
     */
    public $translationForeignKey = 'concern_id';

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
    public $translationModel = ConcernTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(ConcernTranslation::class, 'concern_id','id');
    }
   
}
