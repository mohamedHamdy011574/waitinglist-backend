<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\CateringAddonTranslation;
use Spatie\Permission\Models\Role;
use App\Models\Restaurant;

class CateringAddon extends Model
{
  use Translatable;

    protected $table = 'catering_addons';

    protected $fillable = ['business_id','addon_rate','status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['addon_name', 'description'];

    /**
     * @var string
     */
    public $translationForeignKey = 'catering_addon_id';

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
    public $translationModel = CateringAddonTranslation::class;

    // function for filter records
    public function translation(){
      return $this->hasMany(CateringAddonTranslation::class, 'catering_addon_id','id');
    }

    // function for filter records
    public function role(){
        return $this->belongsTo(Role::class);
    }
    
}
