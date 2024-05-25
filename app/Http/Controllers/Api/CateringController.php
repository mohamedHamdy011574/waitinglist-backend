<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Catering;
use App\Models\CateringMedia;
use App\Models\FavoriteCatering;
use App\Models\CateringCuisine;
use App\Http\Resources\CateringResource;
use App\Http\Resources\CateringDetailResource;
use App\Http\Resources\CateringPackageResource;
use App\Http\Resources\MediaResource;
use DB,Validator,Auth;
use App\Models\Helpers\CateringHelpers;
use App\Models\State;
use App\Models\Setting;

use App\Models\Business;
use App\Models\BusinessCuisine;
use App\Models\FavoriteBusiness;
use App\Models\CateringPackage;
use App\Models\BusinessRatingReview;

class CateringController extends BaseController
{
    use CateringHelpers;
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
     * Catering List.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
      $searchValue = @$request->search;
      $cuisines = $request->cuisines;

      $sort_by = Setting::get('default_sort_by'); // 'alphabetical OR newest';
      $columnSortOrder = Setting::get('default_order'); //'asc OR desc';
      if($request->sort_by && ($request->sort_by == 'alphabetical' || $request->sort_by == 'newest')) {
        $sort_by = $request->sort_by;  
      }
      if($request->order && ($request->order == 'asc' || $request->order == 'desc')){
        $columnSortOrder = $request->order;
      }

      // echo $sort_by. '<br>'; echo $columnSortOrder; die;

      $user = Auth::guard('api')->user();
      // print_r($user); die;

      //Country City Filter
      if($request->city) {
        $validcity = State::where('id', $request->city)->first();
        if($validcity && $validcity->id) {
          if($user) {
            $user->city_id = $request->city;
            $user->save();  
          }
          $city_id = $request->city;  
        } else {
          return $this->sendError('',trans('catering.city_invalid')); 
        }
      } else {
        if($user) {
          if($user->city_id  && $user->city_id != '') {
            $city_id = $user->city_id;
          }
        } else {
          return $this->sendError('',trans('catering.city_required'));
        }  
      }

      $query = Business::query();

      $filter= $query;

      // Search Filter
      if($searchValue != '') {
        $filter = $filter->whereHas('translation',function($query) use ($searchValue){
                    $query
                        ->where('brand_name','like','%'.$searchValue.'%')
                        ->orWhere('description','like','%'.$searchValue.'%');
                      });
      }

      // Cities FIlter
      if($city_id && $city_id != '') {
        $filter = $filter->whereHas('catering_branch', function($qu) use ($city_id){
            $qu->where('state_id', $city_id)->where('status','active');
        });
      }
    
      //Cuisiens Filter
      $cuisines_array = [];  
      if($cuisines && $cuisines != '') {
        $cuisines_array = explode(',', rtrim(trim($cuisines),','));
      }
      $cuisines = $cuisines_array;

      if($cuisines && count($cuisines)) {
        $catr_ids = BusinessCuisine::whereIn('cuisine_id', $cuisines)->pluck('business_id')->toArray();
        $filter = $filter->whereIn('businesses.id', $catr_ids);
      }

      if($sort_by == 'alphabetical') {
        $columnName = 'brand_name';
        $filter = $filter->join('business_translations', 'business_translations.business_id', '=', 'businesses.id')
            ->orderBy('business_translations.'.$columnName, $columnSortOrder)
            ->where('business_translations.locale',\App::getLocale())
            ->select(['businesses.*']);
      } else {
        $columnName = 'created_at';
        $filter = $filter->orderBy($columnName, $columnSortOrder);
      }

      // Checking Subscription
      $filter = $filter->whereHas('vendor', function($qu) {
        $qu->whereHas('subscription', function($quu) {
          $quu->where(['payment_status' => 'paid'])
              ->where('package_end_date', '>=', date('Y-m-d'));
        });
      });

      
      // print_r($filter->get()); die;
      $catering = $filter->paginate();
      if($catering) {
        if(count($catering)) {
          return $this->sendPaginateResponse(CateringResource::collection($catering), trans('catering.success'));
        } else {
          return $this->sendPaginateResponse(CateringResource::collection($catering), trans('catering.not_found'));
        }
      } else {
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }

        /**
    * Add to favorites.
    *
    * @return \Illuminate\Http\Response
    */
    public function add_to_favorites($business_id) {
      $user = Auth::guard('api')->user();
      // print_r($user); die;
      $business = Business::find($business_id);
      if(!$business) {
        return $this->sendError('',trans('catering.not_found')); 
      }
      $myfavs = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'catering'])->get()->count();
      if($myfavs == 0) {
        FavoriteBusiness::create(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'catering']);
      }
      return $this->sendResponse([], trans('catering.added_to_fav'));
    }

    /**
    * Remove from favorites.
    *
    * @return \Illuminate\Http\Response
    */
    public function remove_from_favorites($business_id) {
      $user = Auth::guard('api')->user();
      $business = Business::find($business_id);
      if(!$business) {
        return $this->sendError('',trans('catering.not_found')); 
      }
      $myfavs = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'catering'])->get()->count();
      if($myfavs) {
        FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'catering'])->delete();
      }
      return $this->sendResponse([], trans('catering.removed_from_fav'));
    }

    /**
     * Catering List
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function details($business_id) {
      $user = Auth::guard('api')->user();
      $catering = Business::where(['id' => $business_id, 'status' => 'active'])->first();
      if($catering) {
        return $this->sendResponse(new CateringDetailResource($catering),trans('catering.detail_success'));
      } else {
        return $this->sendError('',trans('catering.not_found')); 
      }
    }


    /**
     * Catering packages
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function packages($business_id, $category_id) {
      $user = Auth::guard('api')->user();
      $packages = CateringPackage::where(['catering_pkg_cat_id' => $category_id,'business_id' => $business_id,  'status' => 'active'])->get();

      if(count($packages)) {
          return $this->sendResponse(CateringPackageResource::collection($packages), trans('catering.package_success'));
      } else {
          return $this->sendResponse(CateringPackageResource::collection($packages), trans('catering.package_not_found'));
      }
    }

    /**
     * Catering Ratings
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function ratings(Request $request) {
        $validator = Validator::make($request->all(),[
          'business_id'   => 'required|exists:businesses,id',
          'service_rating'   => 'required|numeric|min:0|max:5',
          'quality_rating'   => 'required|numeric|min:0|max:5',
          'on_time_rating'   => 'required|numeric|min:0|max:5',
          'presentation_rating'   => 'required|numeric|min:0|max:5',
          'review'   => 'min:3|max:500',
          ]);
        if($validator->fails()){
          return $this->sendValidationError('', $validator->errors()->first());
        }
        $data = $request->all();

        $user = Auth::guard('api')->user();
        $data['customer_id'] = $user->id; 
        $data['branch_type'] = 'catering'; 

        $already_rated = BusinessRatingReview::where(['customer_id' => $user->id, 'branch_type' => 'catering', 'business_id' => $data['business_id'] ])->get()->count();
        if($already_rated > 0) {
          return $this->sendResponse('', trans('catering.already_rated'));
        }

        $rating = BusinessRatingReview::create($data);
        if($rating) {
          return $this->sendResponse('', trans('catering.rating_success'));
        } else {
          return $this->sendResponse('', trans('common.something_went_wrong'));
        }
        // print_r($data); die;
    }



}
