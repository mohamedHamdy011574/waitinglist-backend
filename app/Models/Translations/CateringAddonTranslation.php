<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class CateringAddonTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catering_addon_translations';


    public $translationModel = CateringAddon::class;


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
    protected $fillable = ['addon_name', 'description'];
}
