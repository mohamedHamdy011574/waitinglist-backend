<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class QuizTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quiz_translations';

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
    protected $fillable = ['question', 'option1', 'option2', 'option3', 'option4'];
}
