<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Models\Advertisement;
use App\Models\User;
use App\Http\Resources\AdvertisementResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;
use App\Models\Helpers\CommonHelpers;
use Carbon\Carbon;

class AdvertisementController extends BaseController
{
	use CommonHelpers;
	 /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //$this->middleware('auth');
    }

    // advertisements
    public function advertisements() {
    		$advertisements = Advertisement::where('status','active')
    							->whereDate('duration_from', '<=' ,  date('Y-m-d'))
    							->whereDate('duration_to', '>=',  date('Y-m-d'))
    							->paginate();
        if($advertisements) {
            if($advertisements->count()) {
                return $this->sendPaginateResponse(AdvertisementResource::collection($advertisements),trans('advertisements.success'));
            } else {
                return $this->sendPaginateResponse(AdvertisementResource::collection($advertisements),trans('advertisements.not_found'));
            }
        } else {
            return $this->sendError('',trans('common.something_went_wrong')); 
        }
    }

}
