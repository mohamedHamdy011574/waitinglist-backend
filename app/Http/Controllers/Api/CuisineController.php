<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Cuisine;
use App\Http\Resources\CuisineResource;
use DB,Validator,Auth;

class CuisineController extends BaseController
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
    public function index() {
      $cuisines = CuisineResource::collection(Cuisine::where('status','active')
          ->join('cuisine_translations', 'cuisine_translations.cuisine_id', '=', 'cuisines.id')->orderBy('cuisine_translations.name','asc')
          ->where('cuisine_translations.locale',\App::getLocale())
          ->select(['cuisines.*'])
          ->get());
      if($cuisines) {
        return $this->sendResponse($cuisines, trans('cuisines.cuisines_success'));
      } else {
        return $this->sendError('',trans('cuisines.cuisines_not_found')); 
      }
    }
}
