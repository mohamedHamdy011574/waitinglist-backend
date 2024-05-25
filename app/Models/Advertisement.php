<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\AdvertisementTranslation;
use Spatie\Permission\Models\Role;

class Advertisement extends Model
{
  use Translatable;

    protected $table = 'advertisements';

    protected $fillable = ['added_by', 'logo','video','duration_from', 'duration_to', 'notified_at', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['name'];

    /**
     * @var string
     */
    public $translationForeignKey = 'advertisement_id';

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
    public $translationModel = AdvertisementTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(AdvertisementTranslation::class, 'advertisement_id','id');
    }
}
