<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;

class CityController extends BaseController
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index($country_id = '') {
      if(!$country_id || $country_id == ''){
        return $this->sendError('',trans('cities.country_required'));
      }

      // echo Auth::guard('api')->user()->id; die;
      $user = Auth::guard('api')->user();
      if($user){
        $country = Country::find($country_id);
        if($country){
          $user->country_id = $country_id;
          $user->save();
        }
      }

    	$cities = State::where('country_id',$country_id)->get();
    	$cities_data = StateResource::collection($cities);
      if($cities_data){
        if(count($cities_data) > 0) {
          return $this->sendResponse($cities_data,trans('cities.cities_success'));
        } else {
          return $this->sendResponse('',trans('cities.cities_error')); 
        }
      }else{
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }
}
