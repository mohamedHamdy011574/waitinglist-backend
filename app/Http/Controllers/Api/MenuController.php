<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Models\Business;
use App\Models\MenuCategory;
use App\Models\RestaurantMenu;
use App\Models\FoodTruckMenu;
use App\Http\Resources\MenuCategoryResource;
use App\Http\Resources\MenuItemResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;
use Illuminate\Support\Facades\Input;

class MenuController extends BaseController
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
   * menu_categories List.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function menu_categories($business_id = '') {
    if(!$business_id || $business_id == ''){
      return $this->sendError('',trans('menus.business_required'));
    }
    $business = Business::where(['id' => $business_id])->first();
    if($business) {
      $menu_categories = MenuCategoryResource::collection($business->menus_categories);
      if($menu_categories) {
        if($menu_categories->count()) {
          return $this->sendResponse($menu_categories,trans('menus.menu_categories_success'));
        } else {
          return $this->sendResponse($menu_categories,trans('menus.category_not_found'));
        }
      } else {
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }else{
      return $this->sendError('',trans('menus.business_not_found')); 
    }
  }

   /**
   * menu_categories List.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function menu_items(Request $request) {
    $validator = Validator::make($request->all(), [
          'type' => 'required',
          'menu_category'  => 'required|numeric',
        ]);

    if($validator->fails()) {
        return $this->sendValidationError('', $validator->errors()->first());
    }

    if($request->type == 'restaurant' || $request->type == 'food_truck'){
      if($request->type == 'restaurant') {
        $menu_items = RestaurantMenu::where(['menu_category_id' => $request->menu_category , 'status' => 'active'])->latest()->paginate();
      }

      if($request->type == 'food_truck') {
        $menu_items =  FoodTruckMenu::where(['menu_category_id' => $request->menu_category , 'status' => 'active'])->latest()->paginate();
      }

      $menu_items = MenuItemResource::collection($menu_items);
      if($menu_items) {
        if($menu_items->count()) {
          return $this->sendPaginateResponse($menu_items,trans('menus.menu_items_success'));
        } else {
          return $this->sendPaginateResponse($menu_items,trans('menus.not_found'));
        }
      } else {
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    } else {
      return $this->sendError('',trans('menus.invalid_type')); 
    }

  }

}