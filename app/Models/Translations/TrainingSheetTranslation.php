<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class TrainingSheetTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'training_sheet_translations';

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
    protected $fillable = ['title', 'drill_instructions'];
}
