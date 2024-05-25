<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
         /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    protected $fillable = [
        'user_id','message', 'status', 'title', 'type', 'redirect_to', 'redirect_id',
    ];

    /*public function ins_company()
    {
        return $this->belongsTo(CateringPackageCategory::class,'catering_pkg_cat_id');
    }*/

}
