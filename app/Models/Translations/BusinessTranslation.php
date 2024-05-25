<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class BusinessTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_translations';


    public $translationModel = Business::class;


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
    protected $fillable = ['brand_name', 'description'];
}
