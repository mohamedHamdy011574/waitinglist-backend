<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class PickupHourTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pickup_hour_translations';


    public $translationModel = PickupHour::class;


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shift_name'];
}
