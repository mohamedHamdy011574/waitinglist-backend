<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FavoriteNews;
use Auth;

class NewsResource extends JsonResource
{
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
        $fav = FavoriteNews::where(['customer_id' => @$user->id, 'news_id' => $this->id])->get()->count();
        $is_fav = false;
        if($fav) {
          $is_fav = true;
        }  

        // return parent::toArray($request);
         return [
            'id' => $this->id,
            'image' => asset($this->banner),
            'title' => ucfirst($this->headline),
            'description' => $this->description,
            'is_fav' => $is_fav,
            
        ];
    }
}
