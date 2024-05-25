<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class CateringPacakgeTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catering_package_translations';


    public $translationModel = CateringPackage::class;


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
    protected $fillable = ['package_name','food_serving'];
}
