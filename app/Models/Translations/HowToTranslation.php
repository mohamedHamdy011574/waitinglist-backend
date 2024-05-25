<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class HowToTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'how_to_translations';


    public $translationModel = HowTo::class;

    // function for filter records
    public function how_to(){
        return $this->hasMany(HowTo::class,'id','how_to_id');
    }


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
    protected $fillable = ['question', 'answer'];
}
