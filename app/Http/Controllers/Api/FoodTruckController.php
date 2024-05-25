<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\FoodTruck;
use App\Models\FoodTruckCuisine;
use App\Models\FoodTruckMedia;
use App\Models\FavoriteFoodTruck;
use App\Models\State;
use App\Http\Resources\FoodTruckResource;
use App\Http\Resources\FoodTruckDetailResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\CuisineResource;
use DB,Validator,Auth;
use App\Models\Helpers\FoodTruckHelpers;
use App\Models\Setting;



use App\Models\Business;
use App\Models\BusinessCuisine;
use App\Models\FavoriteBusiness;

class FoodTruckController extends BaseController
{
  use FoodTruckHelpers;
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
     * FoodTruck List
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
          return $this->sendError('',trans('food_trucks.city_invalid')); 
        }
      } else {
        if($user) {
          if($user->city_id  && $user->city_id != '') {
            $city_id = $user->city_id;
          }
        } else {
          return $this->sendError('',trans('food_trucks.city_required'));
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
        $filter = $filter->whereHas('food_truck_branches', function($qu) use ($city_id){
            $qu->where('state_id', $city_id);
        });
      }

      //Cuisiens Filter
      $cuisines_array = [];  
      if($cuisines && $cuisines != '') {
        $cuisines_array = explode(',', rtrim(trim($cuisines),','));
      }
      $cuisines = $cuisines_array;

      if($cuisines && count($cuisines)) {
        $food_truck_ids = BusinessCuisine::whereIn('cuisine_id', $cuisines)->pluck('business_id')->toArray();
        $filter = $filter->whereIn('businesses.id', $food_truck_ids);
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

      $food_trucks = $filter->paginate();
      if($food_trucks) {
        if(count($food_trucks)) {
          return $this->sendPaginateResponse(FoodTruckResource::collection($food_trucks), trans('food_trucks.success'));
        } else {
          return $this->sendPaginateResponse(FoodTruckResource::collection($food_trucks), trans('food_trucks.not_found'));
        }
      } else {
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }

    /**
     * FoodTruck List
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function favorites(Request $request) {
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
          $user->city_id = $request->city;
          $user->save();    
        } else {
          return $this->sendError('',trans('food_trucks.city_invalid')); 
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
      $city_id = $user->city_id;
      if($city_id && $city_id != '') {
        $filter = $filter->whereHas('food_truck_branches', function($qu) use ($city_id){
            $qu->where('state_id', $city_id);
        });
      }

      //Cuisiens Filter
      $cuisines_array = [];  
      if($cuisines && $cuisines != '') {
        $cuisines_array = explode(',', rtrim(trim($cuisines),','));
      }
      $cuisines = $cuisines_array;

      if($cuisines && count($cuisines)) {
        $food_truck_ids = BusinessCuisine::whereIn('cuisine_id', $cuisines)->pluck('business_id')->toArray();
        $filter = $filter->whereIn('businesses.id', $food_truck_ids);
      }


      if($sort_by == 'alphabetical') {
        $columnName = 'name';
        $filter = $filter->join('business_translations', 'business_translations.business_id', '=', 'businesses.id')
            ->orderBy('business_translations.'.$columnName, $columnSortOrder)
            ->where('business_translations.locale',\App::getLocale())
            ->select(['businesses.*']);
      } else {
        $columnName = 'created_at';
        $filter = $filter->orderBy($columnName, $columnSortOrder);
      }

      //Favorites Filter
      $fav_food_truck_ids = FavoriteBusiness::where(['customer_id' => @$user->id, 'type' => 'restaurant'])->pluck('business_id')->toArray();
      $filter = $filter->whereIn('businesses.id', $fav_food_truck_ids);

      // Checking Subscription
      $filter = $filter->whereHas('vendor', function($qu) {
        $qu->whereHas('subscription', function($quu) {
          $quu->where(['payment_status' => 'paid'])
              ->where('package_end_date', '>=', date('Y-m-d'));
        });
      });

      $food_trucks = $filter->paginate();
      if($food_trucks) {
        if(count($food_trucks)) {
          return $this->sendPaginateResponse(FoodTruckResource::collection($food_trucks), trans('food_trucks.success'));
        } else {
          return $this->sendPaginateResponse(FoodTruckResource::collection($food_trucks), trans('food_trucks.not_found'));
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
      $business = Business::find($business_id);
      if(!$business) {
        return $this->sendError('',trans('food_trucks.not_found')); 
      }
      $myfavs = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'food_truck'])->get()->count();
      if($myfavs == 0) {
        FavoriteBusiness::create(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'food_truck']);
      }
      return $this->sendResponse([], trans('food_trucks.added_to_fav'));
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
        return $this->sendError('',trans('food_trucks.not_found')); 
      }
      $myfavs = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'food_truck'])->get()->count();
      if($myfavs) {
        FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'food_truck'])->delete();
      }
      return $this->sendResponse([], trans('food_trucks.removed_from_fav'));
    }



    /**
     * FoodTruck List
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function details(Request $request) {
      $validator=  Validator::make($request->all(),[
          'business_id'   => 'required|numeric',
          'city'   => 'required|exists:states,id',
          ]);
      if($validator->fails()) {
        return $this->sendValidationError('', $validator->errors()->first());
      }

      $user = Auth::guard('api')->user();
      $business = Business::where(['id' => $request->business_id, 'status' => 'active'])->first();


      if($request->city) {
        $validcity = State::where('id', $request->city)->first();
        if($validcity && $validcity->id) {
          if($user) {
            $user->city_id = $request->city;
            $user->save();  
          }
          $city_id = $request->city;  
        } else {
          return $this->sendError('',trans('food_trucks.city_invalid')); 
        }
      } else {
        if($user) {
          if($user->city_id  && $user->city_id != '') {
            $city_id = $user->city_id;
          }
        } else {
          return $this->sendError('',trans('food_trucks.city_required'));
        }  
      }


      if($business && count($business->food_truck_branches)) {
        $business->selected_city_id = $city_id;
        return $this->sendResponse(new FoodTruckDetailResource($business),trans('food_trucks.detail_success'));
      } else {
        return $this->sendError('',trans('food_trucks.not_found')); 
      }
    }

    /**
    * Add to favorites.
    *
    * @return \Illuminate\Http\Response
    */
    public function menu($food_truck_id) {
      $user = Auth::guard('api')->user();
      $food_truck = FoodTruck::find($food_truck_id);
      if(!$food_truck) {
        return $this->sendError('',trans('food_trucks.not_found')); 
      }
      $menu = FoodTruckMedia::where(['media_type' => 'menu', 'food_truck_id' => $food_truck_id])->get();
      return $this->sendResponse([
            'name' => $food_truck->name, 
            'menu' => MediaResource::collection($menu)
          ], trans('food_trucks.menu_success'));
    }
}
