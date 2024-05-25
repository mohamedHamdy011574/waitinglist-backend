<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Models\News;
use App\Models\FavoriteNews;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;
use Illuminate\Support\Facades\Input;

class NewsController extends BaseController
{
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
   * News List.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index(Request $request) {
    $searchValue = $request->get('search');
   	if($searchValue != '') {
			$news = NewsResource::collection(News::wherehas('translation',function($q) use($searchValue) {
		    $q->where('headline','like', '%'.$searchValue.'%')
	      	->orWhere('description','like', '%'.$searchValue.'%');
	      	})
	      	->Where('status','active')
	      	->orderBy('created_at','desc')
          ->paginate());
	      	// ->get());
	      	// print_r($news);die;
    } else {
			$news = NewsResource::collection(News::Where('status','active')
      		->orderBy('created_at','desc')
          ->paginate()); // <- Add this line!
      		// ->get());
	  }
    // $news = $news->paginate();      
    // return $news;
    if($news) {
      if($news->count()) {
        return $this->sendPaginateResponse($news,trans('news.news_success'));
      } else {
        return $this->sendPaginateResponse($news,trans('news.not_found'));
      }
    } else {
      return $this->sendError('',trans('common.something_went_wrong')); 
    }
  }


  /**
   * News Details.
   *
   * @return \Illuminate\Http\Response
   */
  public function details($news_id) {
    $user = Auth::guard('api')->user();
    $news = News::find($news_id);
    if($news) {
      return $this->sendResponse(new NewsResource($news), trans('news.details_success'));
    } else {
      return $this->sendError('',trans('news.not_found')); 
    }
  }


  /**
   * Add to favorites.
   *
   * @return \Illuminate\Http\Response
   */
  public function add_to_favorites($news_id) {
    $user = Auth::guard('api')->user();
    $news = News::find($news_id);
    if(!$news){
      return $this->sendError('',trans('news.not_found')); 
    }
    $myfavs = FavoriteNews::where(['customer_id' => @$user->id, 'news_id' => $news_id])->get()->count();
    if($myfavs == 0){
      FavoriteNews::create(['customer_id' => @$user->id, 'news_id' => $news_id]);
    }
    return $this->sendResponse([], trans('news.added_to_fav'));
  }

  /**
   * Remove from favorites.
   *
   * @return \Illuminate\Http\Response
   */
  public function remove_from_favorites($news_id) {
    $user = Auth::guard('api')->user();
    $news = News::find($news_id);
    if(!$news){
      return $this->sendError('',trans('news.not_found')); 
    }
    $myfavs = FavoriteNews::where(['customer_id' => @$user->id, 'news_id' => $news_id])->get()->count();
    if($myfavs){
      FavoriteNews::where(['customer_id' => @$user->id, 'news_id' => $news_id])->delete();
    }
    return $this->sendResponse([], trans('news.removed_from_fav'));
  }

  /**
     * News List.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function favorites(Request $request) {
      $searchValue = $request->get('search');
      $user = Auth::guard('api')->user();

      if($searchValue != '') {
        $news_data = News::wherehas('translation',function($q) use($searchValue) {
          $q->where('headline','like', '%'.$searchValue.'%')
            ->orWhere('description','like', '%'.$searchValue.'%');
            })
            ->Where('status','active')
            ->orderBy('created_at','desc');
      } else {
        $news_data = News::Where('status','active')
            ->orderBy('created_at','desc');
      }

      //Favorites Filter
      $fav_news = FavoriteNews::where('customer_id', @$user->id)->pluck('news_id')->toArray();
      $news_data = $news_data->whereIn('id', $fav_news)->paginate();



      $news = NewsResource::collection($news_data);
      if($news) {
        if($news->count()) {
          return $this->sendPaginateResponse($news,trans('news.news_success'));
        } else {
          return $this->sendPaginateResponse($news,trans('news.not_found'));
        }
      } else {
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }
}

