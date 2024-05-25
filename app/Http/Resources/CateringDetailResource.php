<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FavoriteBusiness;
use App\Models\Helpers\CateringHelpers;
use App\Models\Catering;
use App\Models\Setting;
use App\Models\BusinessRatingReview;
use Auth;
use Carbon\Carbon;

class CateringDetailResource extends JsonResource
{
  use CateringHelpers;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $user = Auth::guard('api')->user();
      
      //is favorite?
      $fav = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $this->id, 'type' => 'catering'])->get()->count();
      $is_fav = false;
      if($fav) {
        $is_fav = true;
      }

      //getting average ratings
      $service_rating = $quality_rating = $on_time_rating = $presentation_rating = 0;
      if(count($this->catering_ratings_reviews)) {
        foreach($this->catering_ratings_reviews as $rating) {
          $service_rating += $rating->service_rating;
          $quality_rating += $rating->quality_rating;
          $on_time_rating += $rating->on_time_rating;
          $presentation_rating += $rating->presentation_rating;
        }
        $service_rating_display = ceil($service_rating/count($this->catering_ratings_reviews));
        $quality_rating_display = ceil($quality_rating/count($this->catering_ratings_reviews));
        $on_time_rating_display = ceil($on_time_rating/count($this->catering_ratings_reviews));
        $presentation_rating_display = ceil($presentation_rating/count($this->catering_ratings_reviews));

        $ratings = [
          'service_rating' => $service_rating_display,
          'quality_rating' => $quality_rating_display,
          'on_time_rating' => $on_time_rating_display,
          'presentation_rating' => $presentation_rating_display,
        ];

        $total_ratings = $service_rating_display + $quality_rating_display + $on_time_rating_display + $presentation_rating_display;

        $rating_percentage = (100 * $total_ratings) / 20;
      }else{
        $ratings = [
          'service_rating' => 0,
          'quality_rating' => 0,
          'on_time_rating' => 0,
          'presentation_rating' => 0,
        ];
        $rating_percentage = 0;
      }

      $rated = false;
      $ratedbyme = BusinessRatingReview::where(['customer_id' => @$user->id, 'business_id' => $this->id, 'branch_type' => 'catering'])->get()->count();
      if($ratedbyme > 0) {
        $rated = true;
      }



      return [
          'id' => $this->id,
          'name' => $this->brand_name,
          'description' => $this->description,
          // 'food_serving' => $this->food_serving,
          // 'food_serving' => str_replace(' ','',$this->trim_all($this->food_serving)),
          'link' => $this->link,
          // 'address' => $this->catering_branch->address,
          // 'price' => Setting::get('currency').' '.number_format((float)$this->price, 3, '.', ''),
          'status' => $this->status,
          'banner' => New MediaResource($this->get_banners($this->id, 'single')),
          'city' => $this->catering_branch->state_id,
          'all_banners' => MediaResource::collection($this->get_banners($this->id)),
          'is_fav' => $is_fav,
          'timings' => New WorkingHourResource($this->working_hours),
          'delivery_info' => [
            'min_notice' => $this->catering_branch->min_notice.' '.ucfirst($this->catering_branch->min_notice_unit),
            'min_order' => Setting::get('currency').' '.number_format($this->catering_branch->min_order,3, '.', ''),
            'delivery_charge' => ucwords(str_replace('_', ' ', $this->catering_branch->delivery_charge)),
          ],
          'categories' => CateringPackageCategoryResource::collection($this->catering_package_categories),
          'ratings' => $ratings,
          'reviews' => count($this->catering_ratings_reviews),
          'rating_percentage' => ceil($rating_percentage).'%',
          'rated' => $rated,
          /*'plan_served' => New PlanServedResource($this->plan_served),
          'add_ons' => New PlanServedResource($this->addons),*/
      ];
    }
}
