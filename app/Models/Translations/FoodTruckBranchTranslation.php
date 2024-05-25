<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class FoodTruckBranchTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ftruck_branch_translations';


    public $translationModel = FoodTruckBranch::class;


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
    protected $fillable = ['name', 'address'];
}
