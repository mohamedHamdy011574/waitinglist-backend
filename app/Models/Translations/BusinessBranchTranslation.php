<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class BusinessBranchTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_branch_translations';


    public $translationModel = BusinessBranch::class;


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
    protected $fillable = ['branch_name'];
}
