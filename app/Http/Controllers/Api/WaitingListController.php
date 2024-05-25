<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reservation;
use App\Models\BusinessBranch;
use App\Models\Coupon;
use App\Models\Business;
use App\Models\BusinessWorkingHour;
use App\Models\SeatingArea;
use App\Models\WaitingList;
use App\Models\BusinessBranchSeating;
use DB,Validator,Auth;
use App\Models\Helpers\RestaurantHelpers;
use Carbon\Carbon;
use App\Http\Resources\SeatingAreaResource;

class WaitingListController extends BaseController
{	
    use RestaurantHelpers;

    /**
     * Current Waiting List.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function current_waiting_list($business_branch_id)
    {
        try {
            // RESTAURANT BRANCH EXIST ?
            $rest_branch = BusinessBranch::where(['id' => $business_branch_id, 'status' => 'active'])->first();
            if(!$rest_branch) {
                return $this->sendError('',trans('restaurants.not_found')); 
            }

            // IF WAITING LIST ALLOW ?
            if($rest_branch->waiting_list_allow == 0){
                return $this->sendError('',trans('business_branches.no_waiting_list_service'));
            }

            //IS THIS WORKING DAY?
            // $weekday = date('D', strtotime('2020-10-24'));
            // echo $weekday; die;
            $weekday = date('D', strtotime(date('Y-m-d')));
            // echo $rest_branch->business->working_hours; die;
            if($weekday == 'Sun' && @$rest_branch->business->working_hours->sunday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Mon' && @$rest_branch->business->working_hours->monday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Tue' && @$rest_branch->business->working_hours->tuesday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }

            if($weekday == 'Wed' && @$rest_branch->business->working_hours->wednesday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }

            // echo $rest_branch->business->working_hours->thursday_serving;
            if($weekday == 'Thu' && @$rest_branch->business->working_hours->thursday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Fri' && @$rest_branch->business->working_hours->friday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Sat' && @$rest_branch->business->working_hours->saturday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            // echo $weekday; die;

            //CHEKING WORKING HOURS RANGE
            $from_time = $rest_branch->business->working_hours->from_time;
            $to_time = $rest_branch->business->working_hours->to_time;
            $current_time = date('H:i:s');
            //echo $from_time.'<br>';echo $to_time.'<br>';echo $current_time.'<br>';

            $is_ok =  Carbon::now()->between(
                Carbon::parse($from_time), 
                Carbon::parse($to_time)
            );
            if(!$is_ok) {
                return $this->sendError('',trans('business_branches.no_working_hours'));
            }


            $waiting_list = WaitingList::where([
                        'wl_date' => date('Y-m-d'),
                        'business_branch_id' => $business_branch_id,
                        'status' => 'in_queue'
                    ])
                    ->get()
                    ->count();
            // print_r($waiting_list);

            //GETTING SEAING AREAS
            $seatingarea = BusinessBranchSeating::where(['business_branch_id' => $business_branch_id])->pluck('stg_area_id')->toArray();
            if(count($seatingarea)) {
            $seating_areas_data = SeatingAreaResource::collection(SeatingArea::where('status','active')->whereIn('id',$seatingarea)->get());
            }else{
                $seating_areas_data  =[];
            }

            $response_data = [
                'current_waiting_list' => $waiting_list,
                'seating_areas' => $seating_areas_data,
            ];
            return $this->sendResponse($response_data , trans('waiting_list.current_waiting_list')); 
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }
    }

    /**
     * Add me in waiting List
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_me_on_waitinglist(Request $request)
    {
        //VALIDATION ..
        $validator=  Validator::make($request->all(),[
            'business_branch_id' => 'required|numeric', 
            'number_of_people'   => 'required|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendValidationError('', $validator->errors()->first());
        }

        try {
            DB::beginTransaction();
            $data = $request->all();
            $data['date'] = date('Y-m-d');

            //validate seating areas
            $stg_area_ids = @$data['seating_areas'];

            $stg_areas_array = [];  
            $stg_areas = [];

            if($stg_area_ids) {
              if($stg_area_ids && $stg_area_ids != '') {
                  $stg_areas_array = explode(',', rtrim(trim($stg_area_ids),','));
              }
              $stg_area_ids = $stg_areas_array;
              
              if($stg_area_ids && count($stg_area_ids)) {
                  $stg_areas = SeatingArea::whereIn('id', $stg_area_ids)->get();
              }
              
              if(count($stg_areas) == 0)
              {
                  return $this->sendError('',trans('reservations.invalid_seating_area')); 
              }
            }
            

            // RESTAURANT BRANCH EXIST ?
            $rest_branch = BusinessBranch::where(['id' => $data['business_branch_id'], 'status' => 'active'])->first();
            if(!$rest_branch) {
                return $this->sendError('',trans('restaurants.not_found')); 
            }

            // IF WAITING LIST ALLOW ?
            if($rest_branch->waiting_list_allow == 0){
                return $this->sendError('',trans('business_branches.no_waiting_list_service'));
            }

            //IS THIS WORKING DAY?
            // $weekday = date('D', strtotime('2020-10-24'));
            // echo $weekday; die;
            $weekday = date('D', strtotime($data['date']));
            // echo $rest_branch->business->working_hours; die;
            if($weekday == 'Sun' && @$rest_branch->business->working_hours->sunday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Mon' && @$rest_branch->business->working_hours->monday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Tue' && @$rest_branch->business->working_hours->tuesday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }

            if($weekday == 'Wed' && @$rest_branch->business->working_hours->wednesday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }

            // echo $rest_branch->business->working_hours->thursday_serving;
            if($weekday == 'Thu' && @$rest_branch->business->working_hours->thursday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Fri' && @$rest_branch->business->working_hours->friday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            if($weekday == 'Sat' && @$rest_branch->business->working_hours->saturday_serving == 0) {
                return $this->sendError('',trans('business_branches.no_working_day'));
            }
            // echo $weekday; die;

            //CHEKING WORKING HOURS RANGE
            $from_time = $rest_branch->business->working_hours->from_time;
            $to_time = $rest_branch->business->working_hours->to_time;
            $current_time = date('H:i:s');
            //echo $from_time.'<br>';echo $to_time.'<br>';echo $current_time.'<br>';

            $is_ok =  Carbon::now()->between(
                Carbon::parse($from_time), 
                Carbon::parse($to_time)
            );
            if(!$is_ok) {
                return $this->sendError('',trans('business_branches.no_working_hours'));
            }


            //CHECKING ALLOWED CHAIRS
            $reservation_hours = $rest_branch->business->reservation_hours;
            $is_ok = 0;
            $allowed_chairs = 0;
            foreach ($reservation_hours as $r_hour) {
                $is_ok =  Carbon::now()->between(
                    Carbon::parse($r_hour->from_time), 
                    Carbon::parse($r_hour->to_time)
                );
                if($is_ok) {
                    $allowed_chairs = $r_hour->allowed_chair;
                }
            }

            if($allowed_chairs < $data['number_of_people']) { // 20 < 5
                if($allowed_chairs > 0){
                    return $this->sendError('',trans('business_branches.number_of_people_invalid',['allowed_chairs' => $allowed_chairs]));
                }else{
                    return $this->sendError('',trans('business_branches.restaurant_closed_now'));

                }
            }

            //CHECK SAME CUSTOMER ??
            $same_customer_wl = WaitingList::where([
                    'customer_id' => Auth::guard('api')->user()->id,
                    'business_branch_id' => $data['business_branch_id'],
                    'wl_date' => date('Y-m-d'),
                ])
                ->whereIn('status', ['in_queue', 'checked_in'])
                ->get()
                ->count();

            if($same_customer_wl > 0) {
                return $this->sendError('',trans('waiting_list.you_already_in_waiting_list'));
            }    

            // echo $same_customer_wl; die;  


            // GETTING TOKEN NUMBER
            $last_token = WaitingList::where([
                    'business_branch_id' => $data['business_branch_id'],
                    'wl_date' => Carbon::now()->format('Y-m-d'),
                ])->orderBy('created_at','DESC')->first();
            if($last_token) {
               $token = ($last_token->token_number + 1);  
            } else {
               $token = 1; 
            }

            // PUT CUSTOMER IN WAITING LIST
            $insert_data = [
                'customer_id' => Auth::guard('api')->user()->id,
                'first_name' => Auth::guard('api')->user()->first_name,
                'phone_number' => Auth::guard('api')->user()->phone_number,
                'token_number' => $token,
                'business_id' => $rest_branch->business_id,
                'business_branch_id' => $rest_branch->id,
                'reserved_chairs' => $request->number_of_people,
                'wl_datetime' => date('Y-m-d H:i:s'),
                'wl_date' => date('Y-m-d'),
                'wl_time' => date('H:i:s'),
            ];



            $current_number = WaitingList::where([
                    'business_branch_id' => $data['business_branch_id'],
                    'wl_date' => Carbon::now()->format('Y-m-d'),
                    'status' => 'in_queue',
                ])->count();

            $waiting_list = WaitingList::create($insert_data);

            DB::commit();
            $response_data = [
                'queue_number' => $current_number+1,
                'token' => $waiting_list->token_number,
            ];
            return $this->sendResponse($response_data , trans('waiting_list.you_are_added'));
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }

        
    }
}
