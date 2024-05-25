<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Models\Country;
use App\Http\Resources\CountryResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;
class CountryController extends BaseController
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

    public function index(Request $request) {

    	$countries = CountryResource::collection(Country::where('status','active')->get());
      if($countries) {
      	return $this->sendResponse($countries,trans('countries.country_success'));
      } else {
      	return $this->sendError('',trans('countries.country_error')); 
      }
    }
}
