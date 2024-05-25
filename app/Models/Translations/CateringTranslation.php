<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class CateringTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catering_translations';


    public $translationModel = Catering::class;


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
    protected $fillable = ['name', 'description', 'food_serving', 'address'];
}
