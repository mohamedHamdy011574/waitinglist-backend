<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use App\Http\Resources\RestaurantBranchResource;
use App\Http\Resources\CuisineResource;
use DB,Validator,Auth;

class RestaurantBranchController extends BaseController
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
     * Restaurant List
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($restaurant_id) {
      $restaurants_branches = RestaurantBranchResource::collection(RestaurantBranch::where(['status' => 'active', 'restaurant_id' => $restaurant_id])->get());
      if($restaurants_branches) {
        return $this->sendResponse($restaurants_branches, 'Restaurant Branch list got successfully');
     }else {
        return $this->sendError('',trans('Restaurant Branch not found')); 
      }
    }
}
