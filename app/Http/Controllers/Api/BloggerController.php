<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Models\Blogger;
use App\Models\FoodBlog;
use App\Models\Cuisine;
use App\Models\FavouriteFoodBlog;
use App\Models\User;
use App\Http\Resources\BloggerResource;
use App\Http\Resources\FoodBlogResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;
use App\Models\Helpers\CommonHelpers;
use Carbon\Carbon;

class BloggerController extends BaseController
{
	use CommonHelpers;
	 /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
		// list of blogger
    public function index(Request $request) {
    	$searchValue = $request->get('search');
    	if($searchValue != ''){
    		$bloggers = BloggerResource::collection(Blogger::where('blogger_name','like', '%'.$searchValue.'%')
            ->paginate());
    	}else{
    		$bloggers = BloggerResource::collection(Blogger::paginate());
    	}

    	if(count($bloggers) > 0) {
      	return $this->sendPaginateResponse($bloggers,trans('bloggers.blogger_success'));
     	}else {
      	return $this->sendPaginateResponse('',trans('bloggers.bloggers_not_found')); 
    	}
    }

		// become blogger
    public function become_blogger(Request $request){
	    $user = auth()->user();
     	$data = $request->all();
     	$foodblog = Blogger::where('customer_id',$user->id)->count();
 			if($foodblog==0){
				 $validator=  Validator::make($request->all(),[
				'blogger_name' => 'required|min:3|max:100',
				'blogger_photo'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240'
				]);

	    	if($validator->fails()){
	    		return $this->sendValidationError('', $validator->errors()->first());
	  		}
				$data['customer_id'] = $user->id;
	 			if($data['blogger_photo']) {
		     	$path = $this->saveMedia($request->blogger_photo,'blogger_photo');
		     	$data['blogger_photo'] = $path; 
	    	}
 				$blogger = Blogger::create($data);
	 			if($blogger) {
					return $this->sendResponse(new BloggerResource($blogger),trans('bloggers.sucess'));
			  }else {
					return $this->sendResponse('',trans('bloggers.error')); 
				}
	    }else{
				return $this->sendError('',trans('bloggers.already_blogger')); 
			}
    }
    // food post list  
    public function food_blog_index(Request $request) {
    	$searchValue = $request->get('search');
     	if($searchValue != '') {
				$food_blog = FoodBlogResource::collection(FoodBlog::where('recipe_name','like','%'.$searchValue.'%')
	          ->where('status', 'active')
            ->orderBy('created_at', 'desc')
	          ->paginate());
	    } else {
  			$food_blog = FoodBlogResource::collection(FoodBlog::where('status', 'active')->orderBy('created_at','desc')
          	->paginate());
		  }

    	if($food_blog) {
        if($food_blog->count()) {
        	return $this->sendPaginateResponse($food_blog,trans('bloggers.food_blog_success'));
       	} else {
        	return $this->sendPaginateResponse($food_blog,trans('bloggers.food_blog_error'));
        }
     	} else {
      	return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }

    // food post list  
    public function favorite_blogs(Request $request) {
      $searchValue = $request->get('search');
      $user = Auth::guard('api')->user();
      
      if($searchValue != '') {
        $food_blog_data = FoodBlog::
            where('recipe_name','like','%'.$searchValue.'%')
            ->where('status', 'active');    
            // ->paginate();
      } else {
        $food_blog_data = FoodBlog::where('status', 'active')->orderBy('created_at','desc');
            // ->paginate();
      }

      //Favorites Filter
      $fav_blogs = FavouriteFoodBlog::where('customer_id', @$user->id)->pluck('food_blog_id')->toArray();
      $food_blog_data = $food_blog_data->whereIn('id', $fav_blogs)->paginate();


      $food_blogs = FoodBlogResource::collection($food_blog_data);

        if($food_blogs) {
          if($food_blogs->count()) {
            return $this->sendPaginateResponse($food_blogs,trans('bloggers.food_blog_success'));
          } else {
            return $this->sendPaginateResponse($food_blogs,trans('bloggers.food_blog_error'));
          }
        } else {
          return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }

		// add food blog
    public function food_blog(Request $request){
     	$user = auth()->user();
    	$blogger_id = Blogger::where('customer_id',$user->id)->value('id');
     	$become_blogger = Blogger::where('customer_id',$user->id)->value('id');
     	if(!empty($become_blogger) && isset($become_blogger)){
 		     $validator=  Validator::make($request->all(),[
	        'recipe_name' 	=> 'required|min:3|max:50',
	        'description'   => 'required|min:10',
	        'cuisine_id'	=> 'required',
	        'recipe_image'	=>  'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
	        'recipe_video'	=>  'required',
	        ]);
 		    if($validator->fails()){
      		return $this->sendValidationError('', $validator->errors()->first());
      	}
     		$data = $request->all();
     		if($request->cuisine_id){
      			$cuisine_id = Cuisine::where('id',$request->cuisine_id)->count();
	      		if($cuisine_id == 0){
	      			return $this->sendError('',trans('bloggers.cuisine_not_found'));
	      		}
      	}
     		if($request->recipe_name){
      		$recipe_name = FoodBlog::where('added_by',$blogger_id)->Where('recipe_name',$request->recipe_name)->count();
	      		if($recipe_name != 0){
	      			return $this->sendError('',trans('bloggers.already_have_blog'));
	      		}
      	}

     		if(file_exists($data['recipe_video'])) {
		         	$path = $this->saveMedia($request->recipe_video,'recipe_video');
		         	$data['recipe_video'] = $path;
	      }else{
	       		$data['is_link'] = '1';
	      }
		    if($request->recipe_image) {
	         	$path = $this->saveMedia($request->recipe_image,'recipe_image');
	         	$data['recipe_image'] = $path; 
	        	}
     		$data['added_by'] = $become_blogger;
     		$foodblog = FoodBlog::create($data);
     		if($foodblog){
     			return $this->sendResponse(new FoodBlogResource($foodblog),trans('bloggers.added_sucessfully'));
 		    }else{
 		   		return $this->sendResponse('',trans('bloggers.add_blogger_error'));
 		   	}	   
     	}else{
     		return $this->sendError('',trans('bloggers.not_blogger'));
     	}     	
    }
    // food_blog_detail
  	public function food_blog_detail($id = '') {

	    if(!$id || $id == '') {
	     	return $this->sendError('',trans('bloggers.not_found'));
	    }
	    $food_blog = FoodBlog::find($id);
	    if($food_blog) {
	    	return $this->sendResponse(new FoodBlogResource($food_blog),trans('bloggers.successfully'));
	    }else {
	    	return $this->sendError('',trans('bloggers.not_found')); 
	    }
		}
		// update food blogger
    public function food_blog_update(Request $request){
    	$user = auth()->user();
    	// checking particular user post 
    	$blogger_id = Blogger::where('customer_id',$user->id)->value('id');
    	$foodblog = FoodBlog::where('added_by',$blogger_id)->where('id',$request->id)->count();
    	if($foodblog  == 0){
    		return $this->sendError('',trans('bloggers.not_found'));
    	}
    	$validator=  Validator::make($request->all(),[
    	 	'id'			=> 'required',
	        'recipe_name'   => 'required|min:3|max:100',
	        'description'   => 'required|min:10',
	        'cuisine_id'	=> 'required',
	        'recipe_image'	=> 'image|mimes:jpeg,png,jpg,gif,svg|max:10248',
	    ]);
    	if($validator->fails()){
        return $this->sendValidationError('', $validator->errors()->first());
      	}
      	if($request->cuisine_id){
      		$cuisine_id = Cuisine::where('id',$request->cuisine_id)->count();
	      	if($cuisine_id == 0){
	      		return $this->sendError('',trans('bloggers.cuisine_not_found'));
	      		}
      		}
      	if($request->recipe_name){
      		$recipe_name = FoodBlog::where('added_by',$blogger_id)->Where('recipe_name',$request->recipe_name)->where('id','<>',$request->id)->count();
      		if($recipe_name != 0){
      			return $this->sendError('',trans('bloggers.already_have_blog'));
      		}
      	}
    		$data = $request->all();
        $food_blog = FoodBlog::find($request->id);

        if(empty($food_blog)){
        	return $this->sendError('',trans('bloggers.not_found'));
        }

        if(file_exists($request->recipe_video)) {
        	if($food_blog->is_link == 0){
            if(file_exists($food_blog->recipe_video)){
              unlink($food_blog->recipe_video);
            }
	    		}
         	$path = $this->saveMedia($request->recipe_video,'recipe_video');
         	$data['recipe_video'] = $path;
         	$data['is_link'] = '0';
       	}
        
        if(!$request->hasFile('recipe_video') && $request->recipe_video != '') {
          $data['recipe_video'] = $request->recipe_video;
          $data['is_link'] = '1';
        }


	    	if($request->recipe_image) {
		    	if(!empty($food_blog->recipe_image)){
            if(file_exists($food_blog->recipe_image)){
              unlink($food_blog->recipe_image);
            }
		    	}
         	$path = $this->saveMedia($request->recipe_image,'recipe_image');
         	$data['recipe_image'] = $path; 
        }
        if($food_blog->update($data)){
        	return $this->sendResponse(new FoodBlogResource($food_blog),trans('bloggers.update_sucessfully'));
       	}else{
        	return $this->sendResponse('',trans('bloggers.blog_not_updated'));
        }
    }
		// delete food_blog
    public function food_blog_delete($id = ''){
    	if(!$id || $id == ''){
        	return $this->sendError('',trans('bloggers.not_found'));
      }
      $user = auth()->user();
    	// checking particular user post 
    	$blogger_id = Blogger::where('customer_id',$user->id)->value('id');
    	// print_r($blogger_id);die;
    	$foodblog = FoodBlog::where('added_by',$blogger_id)->where('id',$id)->count();
    	if($foodblog  == 0){
    		return $this->sendError('',trans('bloggers.not_found'));
    	}
    	$food_blog = FoodBlog::find($id);
        if(empty($food_blog)){
            return $this->sendResponse('',trans('bloggers.not_found'));
        }
        if($food_blog->delete()){
			    	if($food_blog->is_link == 0){
              if(file_exists($food_blog->recipe_video)){
                unlink($food_blog->recipe_video);
              }
		    		}
		    		if(!empty($food_blog->recipe_image)){
              if(file_exists($food_blog->recipe_image)){
                unlink($food_blog->recipe_image);
              }
		    		}
        	return $this->sendResponse('',trans('bloggers.delete_sucessfully'));
       	}else {
        	return $this->sendResponse('',trans('bloggers.blog_not_deleted'));
        }
    }
		// Added to favorite food post 
    public function add_to_favorites($foodblog_id ='') {
 			if(!$foodblog_id || $foodblog_id == ''){
       	return $this->sendError('',trans('bloggers.not_found'));
    	}
	    $user = Auth::guard('api')->user();
	    $food_blog = FoodBlog::find($foodblog_id);

	    if(!$food_blog){
	    	return $this->sendResponse('',trans('bloggers.not_found')); 
	    }

	    $myfavs = FavouriteFoodBlog::where(['customer_id' => @$user->id, 'food_blog_id' => $foodblog_id])->get()->count();

    	if($myfavs == 0){
      	FavouriteFoodBlog::create(['customer_id' => @$user->id, 'food_blog_id' => $foodblog_id]);
    	}
    		return $this->sendResponse([],trans('bloggers.added_favorites'));
  	}

  	// remove favorited food post  
  	public function remove_from_favorites($foodblog_id ='') {
			if(!$foodblog_id || $foodblog_id == ''){
	    	return $this->sendError('',trans('bloggers.not_found'));
	  	}
	    $user = Auth::guard('api')->user();
	    $food_blog = FoodBlog::find($foodblog_id);
	    if(!$food_blog){
	    	return $this->sendResponse('',trans('bloggers.not_found')); 
	    }
    	$myfavs = FavouriteFoodBlog::where(['customer_id' => @$user->id, 'food_blog_id' => $foodblog_id])->get()->count();
	    if($myfavs){
	      FavouriteFoodBlog::where(['customer_id' => @$user->id, 'food_blog_id' => $foodblog_id])->delete();
	    }
    		return $this->sendResponse([], trans('bloggers.removed_favorites'));
    }

		public function my_food_blog(Request $request) {
			$user = auth()->user();
	    $blogger_id = Blogger::where('customer_id',$user->id)->value('id');
	    	// print_r($blogger_id);die;
			$searchValue = $request->get('search');
	    if($searchValue != ''){
				$food_blog = FoodBlogResource::collection(FoodBlog::Where('added_by',$blogger_id)
		  		->Where('recipe_name','like','%'.$searchValue.'%')
		      ->orWhere('created_at','like','%'.$searchValue.'%')
		      ->orWhere('id','like','%'.$searchValue.'%')
		  		->orderBy('created_at','desc')
		      ->paginate());
				// print_r($food_blog);die;
		  }else{
	  			$food_blog = FoodBlogResource::collection(FoodBlog::where('added_by',$blogger_id)
	  				->orderBy('created_at','desc')
	          ->paginate());
			  	}
	      	if($food_blog) {
		        if($food_blog->count()){
		         	return $this->sendPaginateResponse($food_blog,trans('bloggers.food_blog_success'));
		       	}else{
		         	return $this->sendPaginateResponse($food_blog,trans('bloggers.food_blog_error'));
		        }
	       	}else {
	        	return $this->sendError('',trans('common.something_went_wrong')); 
	      	}
		}

}
