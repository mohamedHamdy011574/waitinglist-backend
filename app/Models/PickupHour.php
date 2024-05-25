<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\PickupHourTranslation;
use Spatie\Permission\Models\Role;

class PickupHour extends Model
{
	use Translatable;
    protected $table = 'pickup_hours';
    protected $fillable = [
  		'business_id','from_time','to_time','pickup_slot_duration','status'
    ];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['shift_name'];

    /**
     * @var string
     */
    public $translationForeignKey = 'pkp_hour_id';

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
    public $translationModel = PickupHourTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(PickupHourTranslation::class, 'pkp_hour_id','id');
    }
   
}
