<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FavouriteFoodBlog;
use App\Models\FoodBlog;
use App\Models\Blogger;
use App\Models\Cuisine;
use Auth;

class FoodBlogResource extends JsonResource
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
        $fav = FavouriteFoodBlog::where(['customer_id' => @$user->id, 'food_blog_id' => $this->id])->get()->count();
        $is_fav = false;
        if($fav) {
          $is_fav = true;
        }

        //is_ my Blog?
        $my_blog = false;
        if($user){
          $blogger_id = Blogger::where('customer_id',$user->id)->first();
          if($this->added_by == @$blogger_id->id){
            $my_blog = true;
          }
        }

        // is link
        $recipe_video = FoodBlog::where('id',$this->id)->value('is_link');
        if($recipe_video == 1){
           $video = $this->recipe_video;            
        }
        else{
          $video = asset($this->recipe_video);
        }
        
        $date  = ($this->created_at->todatestring());
        return [
            'id' => $this->id,
            'recipe_name' => ucfirst($this->recipe_name),
            'cuisine'  => new CuisineResource(Cuisine::find($this->cuisine_id)),
            'recipe_image' => asset($this->recipe_image),
            'recipe_video' => $video ,
            'description' => $this->description,
            'date'  => date("d-M-Y", strtotime($date)),
            'is_fav' => $is_fav,
            'my_blog' => $my_blog,
            
        ];
    }
}  