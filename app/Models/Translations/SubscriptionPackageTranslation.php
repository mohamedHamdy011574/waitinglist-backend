<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPackageTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sub_package_translations';


    public $translationModel = SubscriptionPackage::class;


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
    protected $fillable = ['package_name', 'description'];
}
