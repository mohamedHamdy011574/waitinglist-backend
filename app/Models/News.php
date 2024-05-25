<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\NewsTranslation;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;


class News extends Model implements HasMedia
{
	use Translatable ,HasMediaTrait;

 	protected $fillable = ['status','banner'];
    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['headline','description'];

    /**
     * @var string
     */
    public $translationForeignKey = 'news_id';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The class name for the localed model.
     *
     * @var string
     */
    public $translationModel = NewsTranslation::class;

    // function for filter records
    public function translation(){
    	return $this->hasMany(NewsTranslation::class, 'news_id','id');
    }


     public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300);
    }
}

