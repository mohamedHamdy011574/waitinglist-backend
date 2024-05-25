<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class CateringPlanTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catering_plan_translations';


    public $translationModel = CateringPlan::class;


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
    protected $fillable = ['plan_name', 'description', 'food_serving'];
}
