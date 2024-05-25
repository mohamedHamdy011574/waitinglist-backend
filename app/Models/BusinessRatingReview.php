<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessRatingReview extends Model
{
		protected $table = 'business_ratings_reviews';
    protected $fillable = [
  		'customer_id','business_id','branch_type','service_rating','quality_rating','on_time_rating','presentation_rating','review','status'];
}
