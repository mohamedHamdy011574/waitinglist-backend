<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class FoodTruckMenuTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'food_truck_menu_translations';


    public $translationModel = FoodTruckMenu::class;


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
