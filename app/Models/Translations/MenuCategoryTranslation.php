<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class MenuCategoryTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_category_translations';


    public $translationModel = MenuCategory::class;



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
