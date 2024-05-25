<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantCuisine;
use App\Models\RestaurantMedia;
use App\Models\FavoriteRestaurant;
use App\Models\RestaurantCoupon;
use App\Models\RestaurantBranch;
use App\Models\RestaurantWorkingHour;
use App\Models\RestaurantBranchSeating;
use App\Models\SeatingArea;
use App\Models\Coupon;
use App\Models\State;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RestaurantDetailResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\CuisineResource;
use App\Http\Resources\CouponResource;
use App\Http\Resources\WorkingHourResource;
use App\Http\Resources\SeatingAreaResource;
use DB,Validator,Auth;
use App\Models\Helpers\RestaurantHelpers;
use Carbon\Carbon;
use App\Models\Setting;



use App\Models\Business;
use App\Models\BusinessBranch;
use App\Models\BusinessCuisine;
use App\Models\FavoriteBusiness;
use App\Models\BusinessCoupon;
use App\Models\BusinessBranchSeating;
use App\Models\BusinessWorkingHour;


class RestaurantController extends BaseController
{
  use RestaurantHelpers;
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
     * Restaurant List
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
          return $this->sendError('',trans('restaurants.city_invalid'));
        }
      } else {
        if($user) {
          if($user->city_id  && $user->city_id != '') {
            $city_id = $user->city_id;
          }
        } else {
          return $this->sendError('',trans('restaurants.city_required'));
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
        $filter = $filter->whereHas('restaurant_branches', function($qu) use ($city_id){
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
        $rest_ids = BusinessCuisine::whereIn('cuisine_id', $cuisines)->pluck('business_id')->toArray();
        $filter = $filter->whereIn('businesses.id', $rest_ids);
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

      $filtered_restaurants = $filter->get();

      $restaurant_array = array();
      foreach ($filtered_restaurants as $key => $rest) {
        if(count($rest->restaurant_branches) > 0){
          if(!in_array($rest->id, $restaurant_array)){
            array_push($restaurant_array, $rest->id);
          }
        }
      }
      $restaurants = Business::whereIn('id',$restaurant_array)->paginate();
      // return $restaurants;
      if($restaurants) {
        if(count($restaurants)) {
          return $this->sendPaginateResponse(RestaurantResource::collection($restaurants), trans('restaurants.success'));
        } else {
          return $this->sendPaginateResponse(RestaurantResource::collection($restaurants), trans('restaurants.not_found'));
        }
      } else {
        return $this->sendError('',trans('common.something_went_wrong'));
      }
    }

    /**
     * Restaurant List
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
          return $this->sendError('',trans('restaurants.city_invalid'));
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
        $filter = $filter->whereHas('restaurant_branches', function($qu) use ($city_id){
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
        $rest_ids = BusinessCuisine::whereIn('cuisine_id', $cuisines)->pluck('business_id')->toArray();
        $filter = $filter->whereIn('businesses.id', $rest_ids);
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

      //Favorites Filter
      $fav_rest_ids = FavoriteBusiness::where(['customer_id' => @$user->id, 'type' => 'restaurant'])->pluck('business_id')->toArray();
      $filter = $filter->whereIn('businesses.id', $fav_rest_ids);

      // Checking Subscription
      $filter = $filter->whereHas('vendor', function($qu) {
        $qu->whereHas('subscription', function($quu) {
          $quu->where(['payment_status' => 'paid'])
              ->where('package_end_date', '>=', date('Y-m-d'));
        });
      });

      $restaurants = $filter->paginate();
      if($restaurants) {
        if(count($restaurants)) {
          return $this->sendPaginateResponse(RestaurantResource::collection($restaurants), trans('restaurants.success'));
        } else {
          return $this->sendPaginateResponse(RestaurantResource::collection($restaurants), trans('restaurants.not_found'));
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
        return $this->sendError('',trans('restaurants.not_found'));
      }
      $myfavs = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'restaurant'])->get()->count();
      if($myfavs == 0) {
        FavoriteBusiness::create(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'restaurant']);
      }
      return $this->sendResponse([], trans('restaurants.added_to_fav'));
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
        return $this->sendError('',trans('restaurants.not_found'));
      }
      $myfavs = FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'restaurant'])->get()->count();
      if($myfavs) {
        FavoriteBusiness::where(['customer_id' => @$user->id, 'business_id' => $business_id, 'type' => 'restaurant'])->delete();
      }
      return $this->sendResponse([], trans('restaurants.removed_from_fav'));
    }



    /**
     * Restaurant List
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
      $business = Business::with(['restaurant_branches' => function($q){
        $q->where('status','active');
      }])->where(['id' => $request->business_id, 'status' => 'active'])->first();
      //dd($business->restaurant_branches);
      if($request->city) {
        $validcity = State::where('id', $request->city)->first();
        if($validcity && $validcity->id) {
          if($user) {
            $user->city_id = $request->city;
            $user->save();
          }
          $city_id = $request->city;
        } else {
          return $this->sendError('',trans('restaurants.city_invalid'));
        }
      } else {
        if($user) {
          if($user->city_id  && $user->city_id != '') {
            $city_id = $user->city_id;
          }
        } else {
          return $this->sendError('',trans('restaurants.city_required'));
        }
      }
      /*if(!$business) {
        return $this->sendError('',trans('restaurants.not_found'));
      } else if((count($business->restaurant_branches)) == 0){
        return $this->sendError('',trans('restaurants.branch_not_found'));
      } else {
        $business->selected_city_id = $city_id;
        return $this->sendResponse(new RestaurantDetailResource($business),trans('restaurants.detail_success'));
      }*/

      if($business && count($business->restaurant_branches)) {
        $business->selected_city_id = $city_id;
        return $this->sendResponse(new RestaurantDetailResource($business),trans('restaurants.detail_success'));
      } else if(!$business){
        return $this->sendError('',trans('restaurants.not_found'));
      } else if((count($business->restaurant_branches)) == 0){
        return $this->sendError('',trans('restaurants.branch_not_found'));
      }
    }

    /**
    * menus.
    *
    * @return \Illuminate\Http\Response
    */
    public function menu($restaurant_id) {
      $user = Auth::guard('api')->user();
      $restaurant = Restaurant::find($restaurant_id);
      if(!$restaurant) {
        return $this->sendError('',trans('restaurants.not_found'));
      }
      $menu = RestaurantMedia::where(['media_type' => 'menu', 'restaurant_id' => $restaurant_id])->get();
      return $this->sendResponse([
            'name' => $restaurant->name,
            'menu' => MediaResource::collection($menu)
          ], trans('restaurants.menu_success'));
    }

    /**
    * coupons
    *
    * @return \Illuminate\Http\Response
    */
    public function coupons(Request $request) {

      $validator=  Validator::make($request->all(),[
          'business_branch_id'   => 'required|numeric',
          'date'   => 'required|date|date_format:Y-m-d',
          ]);
      if($validator->fails()) {
        return $this->sendValidationError('', $validator->errors()->first());
      }

      if(date('Y-m-d') > $request->date) {
        return $this->sendError('',trans('restaurants.its_old_date'));
      }

      $restaurant_branch = BusinessBranch::find($request->business_branch_id);
      if(!$restaurant_branch) {
        return $this->sendError('',trans('restaurants.not_found'));
      }

      //coupons
      $coupons_ids = BusinessCoupon::where('business_id', $restaurant_branch->business_id)->pluck('coupon_id')->toArray();

      $coupons = Coupon::whereIn('id',$coupons_ids)
                  ->whereDate('end_date', '>=', $request->date)
                  ->where(['active' => 1])
                  ->get();
                  // ->toSql();
      if(count($coupons)) {
        return $this->sendResponse(CouponResource::collection($coupons), trans('coupons.success'));
      } else {
        return $this->sendResponse('', trans('coupons.not_found'));
      }
    }

    /**
    * working_days
    *
    * @return \Illuminate\Http\Response
    */
    public function working_days($business_branch_id = '') {
      $rest_branch = BusinessBranch::where(['id' => $business_branch_id, 'status' => 'active'])->first();

      if(!$rest_branch) {
        return $this->sendError('',trans('restaurants.not_found'));
      }

      // seatingarea
      if($business_branch_id) {
        $seatingarea = BusinessBranchSeating::where(['business_branch_id' => $business_branch_id])->pluck('stg_area_id')->toArray();
      }

      // working_days
      $working_days = BusinessWorkingHour::where('business_id', $rest_branch->business_id)->first();

      $working_days_data = [
              'sunday_serving' => $working_days->sunday_serving,
              'monday_serving' => $working_days->monday_serving,
              'tuesday_serving' => $working_days->tuesday_serving,
              'wednesday_serving' => $working_days->wednesday_serving,
              'thursday_serving' => $working_days->thursday_serving,
              'friday_serving' => $working_days->friday_serving,
              'saturday_serving' => $working_days->saturday_serving,
            ];

      if(count($seatingarea)) {
        $seating_areas = SeatingAreaResource::collection(SeatingArea::where('status','active')->whereIn('id',$seatingarea)->get());
        // return $this->sendResponse(['working_days' => $working_days_data, 'seating_areas' => $seating_areas, 'total_seats' => $rest_branch->total_seats], trans('restaurants.booking_info_success'));
        return $this->sendResponse([
            'working_days' => $working_days_data,
            'seating_areas' => $seating_areas,
            // 'total_seats' => $rest_branch->total_seats
          ], trans('restaurants.booking_info_success'));
      } else {
        return $this->sendError('',trans('restaurants.not_found'));
      }

    }





}
