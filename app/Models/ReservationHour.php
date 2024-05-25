<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\ReservationHourTranslation;
use Spatie\Permission\Models\Role;

class ReservationHour extends Model
{
	use Translatable;
    protected $table = 'reservation_hours';
    protected $fillable = [
  		'business_id','from_time','to_time','allowed_chair','dining_slot_duration','status'
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
    public $translationForeignKey = 'res_hour_id';

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
    public $translationModel = ReservationHourTranslation::class;

    // translation
    public function translation(){
      return $this->hasMany(ReservationHourTranslation::class, 'res_hour_id','id');
    }
   
}
