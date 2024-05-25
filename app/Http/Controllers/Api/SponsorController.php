<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Models\Sponsor;
use App\Models\User;
use App\Http\Resources\SponsorResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;
use App\Models\Helpers\CommonHelpers;
use Carbon\Carbon;

use App\Jobs\SendSponsorNotificationJob;


class SponsorController extends BaseController
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

		// become sponsor
    public function become_sponsor(Request $request){
	    $user = auth()->user();
     	$data = $request->all();
     	$is_sponsor = Sponsor::where(['customer_id' => $user->id, 'status' => 'active'])->count();
 		  if($is_sponsor==0) {
  			$validator=  Validator::make($request->all(),[
  			    'video'=>'required|file|max:10240',
  			    'first_name' => 'required|min:3|max:100',
            'last_name' => 'required|min:3|max:100',
            'contact_number' => 'required|numeric',
            'email' => 'required|email|max:100',
            'duration_from' => 'required|date|date_format:Y-m-d|after:yesterday',
            'duration_to' => 'required|date|date_format:Y-m-d|after:duration_from',
  			]);

	    	if($validator->fails()) {
	    		return $this->sendValidationError('', $validator->errors()->first());
	  		}
  			$data['customer_id'] = $user->id;
   			if($data['video']) {
		     	$path = $this->saveMedia($request->video,'sponsor_video');
		     	$data['video'] = $path; 
    	  }
        $sponsor = Sponsor::create($data);
        $sponsor = Sponsor::find($sponsor->id);
 			  if($sponsor) {
            if($sponsor->duration_from == date('Y-m-d')) {
              //notifications
              SendSponsorNotificationJob::dispatch($sponsor)->delay(now()->addSeconds(5));
              /*$users = User::where('user_type','Customer')
                  ->where(['verified' => 1, 'status' => 'active'])
                  // ->whereIn('id', ['107'])
                  ->get();
              foreach ($users as $user) {
                $title = trans('sponsors.notification_title');
                $body = trans('sponsors.notification_body');
                $nres = $this->sendNotification($user,$title,$body,"sponsor",$sponsor->id);
                // echo '<pre>'; print_r($nres); die;
              } */
              $sponsor->notified_at = Carbon::now();  
              $sponsor->save(); 
            }
				    return $this->sendResponse(new SponsorResource($sponsor),trans('sponsors.become_success'));
		    } else {
				  return $this->sendResponse('',trans('sponsors.error')); 
			  }
	    } else {
			  return $this->sendError('',trans('sponsors.already_sponsor')); 
		  }
    }


    // sponsors
    public function sponsors() {
    		$sponsors = Sponsor::where('status','active')
    							->whereDate('duration_from', '<=' ,  date('Y-m-d'))
    							->whereDate('duration_to', '>=',  date('Y-m-d'))
    							->paginate();
        if($sponsors) {
            if($sponsors->count()) {
                return $this->sendPaginateResponse(SponsorResource::collection($sponsors),trans('sponsors.success'));
            } else {
                return $this->sendPaginateResponse(SponsorResource::collection($sponsors),trans('sponsors.not_found'));
            }
        } else {
            return $this->sendError('',trans('common.something_went_wrong')); 
        }
    }

}
