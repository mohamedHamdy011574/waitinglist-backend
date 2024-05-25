<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class CateringPacakgeCategoriesTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catering_pkg_cat_translations';


    public $translationModel = CateringPackageCategory::class;


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
    protected $fillable = ['name'];
}
