<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenuTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurant_menu_translations';


    public $translationModel = RestaurantMenu::class;


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
    protected $fillable = ['name', 'description'];
}
