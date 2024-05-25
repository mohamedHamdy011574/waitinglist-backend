<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class ConcernTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'concern_translations';


    public $translationModel = Concern::class;


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
    protected $fillable = ['concern'];
}
