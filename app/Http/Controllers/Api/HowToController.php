<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Http\Resources\HowToResource;
use App\Models\HowTo;
use DB,Validator,Auth;

class HowToController extends BaseController
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
      $howtos = HowTo::where('status',1)->orderBy('display_order','asc')->paginate();
      // $howtos = HowToResource::collection($howtos);
      // return $howtos;
      if($howtos) {
        if(count($howtos)) {
          $howtos = HowToResource::collection($howtos);
          return $this->sendPaginateResponse($howtos, trans('how_to.success'));
        } else {
          return $this->sendPaginateResponse($howtos, trans('how_to.not_found'));
        }
      } else {
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }
}
