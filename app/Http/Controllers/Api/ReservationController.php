<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\SeatingArea;
use App\Models\ReservationSeatingArea;
use App\Models\BusinessBranch;
use App\Models\BusinessWorkingHour;
use App\Models\Reservation;
use App\Models\ReservationHour;
use App\Models\BusinessCoupon;
use App\Models\Setting;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\ReservationHistoryResource;
use App\Http\Resources\RestaurantBranchResource;
use DB,Validator,Auth;
use App\Models\Helpers\RestaurantHelpers;
use App\Models\Helpers\ReservationHelpers;
use Carbon\Carbon;

class ReservationController extends BaseController
{	
    use RestaurantHelpers, ReservationHelpers;

    /**
    * AVAILABLE_TIMES
    *
    * @return JSON
    */
	public function available_times(Request $request) 
    {
        //VALIDATION ..
        $validator = Validator::make($request->all(),[
            'business_branch_id' => 'required|numeric',
            'date'               => 'required|date|date_format:Y-m-d',
            'number_of_people'   => 'required|numeric|max:10000',
        ]);

        if($validator->fails()) {
            return $this->sendValidationError('', $validator->errors()->first());
        }

        if(date('Y-m-d') > $request->date) {
            return $this->sendError('',trans('restaurants.its_old_date'));
        }

    	  $rest_branch = BusinessBranch::where(['id' => $request->business_branch_id, 'status' => 'active'])->first();
        if(!$rest_branch) {
            return $this->sendError('',trans('restaurants.not_found')); 
        }

        if(!$rest_branch->reservation_allow) {
            return $this->sendError('',trans('reservations.reservation_not_allowed'));
        }

        // DATE RESERVABLE ?
        $weekday = date('N', strtotime($request->date));
        // echo "<pre>";print_r($weekday);exit;
        $working_days = BusinessWorkingHour::where('business_id', $rest_branch->business_id)->first();
        if(!$working_days) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }
        $working_days_array = $this->get_working_days($working_days);

        if(!in_array($weekday, $working_days_array)) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }

        //RESTAURANT IS AVAILABLE ?
        $restaurant = Business::where(['id' => $rest_branch->business_id, 'status' => 'active'])->first();
        if(!$restaurant) {
          return $this->sendError('',trans('restaurants.not_found')); 
        }

        if($request->date == date('Y-m-d') && $restaurant->working_status != 'available') {
          return $this->sendError('',trans('restaurants.bookings_disabled')); 
        }

        $requestArr = $request->all();
        $reservable_slots = $this->getAvailableTimeSlots($requestArr,$restaurant->id);
        $reservable_time_slots_p1 = [];
        foreach ($reservable_slots['reservable_time_slots'] as $kres => $vres) {
            $res_time = [];
            $res_time['time'] = $vres['start_time'];
            $res_time['send_time'] = $vres['send_start_time'];
            array_push($reservable_time_slots_p1, $res_time);
        }

        if($reservable_time_slots_p1)
        {        
            return $this->sendResponse($reservable_time_slots_p1, trans('reservations.available_slots'));
        }
        else
        {
            if($reservable_slots['msg'] != '')
            {
              return $this->sendError('', $reservable_slots['msg']);
            }
            return $this->sendError('', trans('reservations.slots_not_available'));
        }      
    }

    /**
     * Reservation
     *
     * @return JSON
     */
    public function reservation(Request $request)
    {
        //VALIDATION ..
        $validator=  Validator::make($request->all(),[
            'business_branch_id' => 'required|numeric',
            'date'               => 'required|date|date_format:Y-m-d',
            'number_of_people'   => 'required|numeric|max:10000',
            'time'               => 'required|date_format:H:i:s',
            'coupon_id'          => 'numeric|nullable',
        ]);

        if($validator->fails()) {
            return $this->sendValidationError('', $validator->errors()->first());
        }

        //validate seating areas
        $stg_area_ids = $request->seating_areas;

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

        // check if the table already reserved for given time and slot
        $reservation = Reservation::where('business_branch_id',$request->business_branch_id)
                ->where('customer_id',Auth::guard('api')->user()->id)
                ->where('due_date',$request->date)
                ->where('due_time',$request->time)
                ->where('status','confirmed')
                ->first();

        if($reservation)
        {
            return $this->sendError('', trans('reservations.already_reserved',['date' => $request->date,'time' => date('h:i A',strtotime($request->time))]));
        }
        
        //vallidate restaurant timings
        if(date('Y-m-d') > $request->date) {
            return $this->sendError('',trans('restaurants.its_old_date'));
        }

        $restBranch = BusinessBranch::where(['id' => $request->business_branch_id, 'status' => 'active'])->first();
        if(!$restBranch) {
            return $this->sendError('',trans('restaurants.not_found')); 
        }

        //validate coupon
        if($request->coupon_id)
        {
            $coupon = BusinessCoupon::where('coupon_id',$request->coupon_id)->where('business_id',$restBranch->business_id)->first();
            if(!$coupon)
            {
                return $this->sendError('',trans('reservations.invalid_coupon'));
            }
        }

        // DATE RESERVABLE ?
        $weekday = date('N', strtotime($request->date));
        $working_days = BusinessWorkingHour::where('business_id', $restBranch->business_id)->first();
        if(!$working_days) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }
        $working_days_array = $this->get_working_days($working_days);

        if(!in_array($weekday, $working_days_array)) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }

        //RESTAURANT IS AVAILABLE ?
        $restaurant = Business::where(['id' => $restBranch->business_id, 'status' => 'active'])->first();
        if(!$restaurant) {
          return $this->sendError('',trans('restaurants.not_found')); 
        }

        if($request->date == date('Y-m-d') && $restaurant->working_status != 'available') {
          return $this->sendError('',trans('restaurants.bookings_disabled')); 
        }

        $requestArr = $request->all();
        $reservable_slots = $this->getAvailableTimeSlots($requestArr,$restaurant->id);
        if(!array_key_exists($request->time, $reservable_slots['reservable_time_slots']))
        {
            if($reservable_slots['msg'] != '')
            {
              return $this->sendError('', $reservable_slots['msg']);
            }
            return $this->sendError('',trans('reservations.slots_not_available')); 
        }
        else
        {
          $dining_slot_duration = $reservable_slots['reservable_time_slots'][$request->time]['dining_slot_duration']; //in mins

        }
        $data = [
            'customer_id' => Auth::guard('api')->user()->id,
            'first_name' => Auth::guard('api')->user()->first_name,
            'phone_number' => Auth::guard('api')->user()->phone_number,
            'business_id' => $restBranch->business_id,
            'business_branch_id' => $restBranch->id,
            'coupon_id' => $request->coupon_id,
            'reserved_chairs' => $request->number_of_people,
            'check_in_date' => $request->date.' '.$request->time,
            'due_date' => $request->date,
            'due_time' => $request->time,
            'end_time' => date("H:i", strtotime('+'.$dining_slot_duration.' minutes', strtotime($request->time))),
            'status' => 'confirmed'
        ];
        
        if($reservation = Reservation::create($data)) {

            foreach ($stg_areas as $vstr) {
              
                $resseatig = ReservationSeatingArea::create(['reservation_id' => $reservation->id, 'stg_area_id' => $vstr->id]);
            }
            return $this->sendResponse(new ReservationResource($reservation), trans('reservations.added'));
        } else {
            return $this->sendResponse('', trans('reservations.error'));
        }
    }

    /**
     * Cancel Reservation
     *
     * @return JSON
     */
    public function cancel(Request $request) {
      //VALIDATION ..
        $validator=  Validator::make($request->all(),[
            'reservation_id'   => 'required|numeric|exists:reservations,id',
        ]);

        if($validator->fails()) {
            return $this->sendValidationError('', $validator->errors()->first());
        }

        $user = Auth::user();
        $reservation = Reservation::find($request->reservation_id);
        if($reservation->customer_id != $user->id){
          return $this->sendError('',trans('reservations.not_booked_by_you'));
        }
        
        if(now() > $reservation->check_in_date) {
            return $this->sendError('',trans('reservations.old_reservation_cant_cancelled'));
        } else {
          if($reservation->status == Reservation::CANCELLED_STATUS) {
            return $this->sendError('',trans('reservations.reservation_already_cancelled'));
          } 
          $reservation->status = Reservation::CANCELLED_STATUS;
          if($reservation->save()) {
            return $this->sendResponse(new ReservationResource($reservation), trans('reservations.cancelled'));
          } else {
            return $this->sendError('',trans('common.something_went_wrong')); 
          }
          
        } 
    }

    /**
     * Reservation History
     *
     * @return JSON
     */
    public function reservation_history() {
      $user = Auth::user();
      $reservations = Reservation::where('customer_id',$user->id)->orderBy('check_in_date',
        'desc')->paginate();
      // return $user->reservations;
      $my_reservations = ReservationHistoryResource::collection($reservations);
      if($my_reservations->count()){
        return $this->sendPaginateResponse($my_reservations,trans('reservations.success'));
      } else {
        return $this->sendPaginateResponse($my_reservations,trans('reservations.not_found'));
      }
    }

    /**
     * Reservation History
     *
     * @return JSON
     */
    public function reservation_history_mw() {
      $user = Auth::user();
      // $reservations = Reservation::where(['customer_id' => $user->id])
      //                           ->whereYear('check_in_date', '=', $request->year)
      //                           ->whereMonth('check_in_date', '=', $request->month)
      //                           ->orderBy('check_in_date', 'desc')
      //                           ->get();

      $reservations = Reservation::where(['customer_id' => $user->id])
                    ->orderBy('check_in_date', 'desc')
                    ->get()
                    ->groupBy(function ($val) {
                        return Carbon::parse($val->check_in_date)->format('Y-m');
                      });
      $reservations_formatted = [];    

      foreach ($reservations as $month_year => $history) {
        $historydata = ReservationHistoryResource::collection($history);
        $array = [
                    "month" => $month_year,
                    "history" => $historydata,
                ];               
        array_push($reservations_formatted, $array);
      }

      if(count($reservations_formatted)) {
        return $this->sendResponse($reservations_formatted,trans('reservations.success'));
      } else {
        return $this->sendResponse($reservations_formatted,trans('reservations.not_found'));
      }
    }

    /**
     * Reservation Details
     *
     * @return JSON
     */
    public function reservation_detail($id) {
      $user = Auth::user();
      $reservation = Reservation::where(['customer_id' => $user->id, 'id' => $id])->first();
      if($reservation){
        return $this->sendResponse(new ReservationResource($reservation), trans('reservations.details_success'));
      } else {
        return $this->sendPaginateResponse('',trans('reservations.not_found'));
      }
    }

    public function getAvailableTimeSlots($requestArr,$business_id)
    {
        //getting all reservation hours

        $res_hours = ReservationHour::where('business_id',$business_id)->where('status','active')->get();
        
        $res_hrs_arr = array();
        foreach ($res_hours as $res_hr) {
          // echo "<pre>";print_r($res_hr);exit;
            $rbranch_working_from = $res_hr->from_time;
            $rbranch_working_to = date("H:i:s", strtotime($res_hr->to_time) - ($res_hr->dining_slot_duration * 60));

            $res_hrs_arr[$res_hr->id] = $this->splitTime($requestArr['date'].' '.$rbranch_working_from, $requestArr['date'].' '.$rbranch_working_to, $res_hr->dining_slot_duration);

        }
        $time_slots = array();
        foreach ($res_hrs_arr as $res_hrs) {
            foreach ($res_hrs as $res_key => $res_value) {
                $time_slots[$res_key] = $res_value;
            }
        }
        ksort($time_slots);        
        
        $reserved_time_range = Reservation::where([
                                      'business_branch_id' => $requestArr['business_branch_id'],
                                      'due_date' => $requestArr['date'],
                                  ])
                                  ->whereIn('status', ['confirmed', 'checked_in'])
                                  ->select(['due_time', 'end_time', 'reserved_chairs'])
                                  ->orderBy('check_in_date')
                                  ->get()
                                  ->toArray();
        // echo "<pre>";print_r($reserved_time_range);exit;
        $reserved_time_slots = [];
        $min_slot_duration = 15; //in mins
        foreach ($reserved_time_range as $rte) {
          // echo "<pre>";print_r($dining_slot_duration);exit;
            $slots = []; 
            $slots =  $this->splitTimeRezerved($requestArr['date'].' '.$rte['due_time'], $requestArr['date'].' '.$rte['end_time'], $min_slot_duration);
            foreach ($slots as $s => $v) {
                $reserved_time_slots[$s] = $v;             
            }
        }
        // echo "<pre>";print_r($reserved_time_slots);exit;
        $reservable_time_slots = [];
        $msg = '';
        foreach($time_slots as $kts=>$vts)
        {
            if(!array_key_exists($kts, $reserved_time_slots)){

                //check if nubmer of people allowed for the slot
                foreach ($res_hrs_arr as $res_hr_key => $res_value) {
                    if(array_key_exists($kts, $res_value)){
                        $allowed_chair = ReservationHour::find($res_hr_key)->allowed_chair;
                        $dining_slot_duration = ReservationHour::find($res_hr_key)->dining_slot_duration;
                        if($requestArr['number_of_people'] <= $allowed_chair)
                        {
                          $time_slot_arr = [];
                          $time_slot_arr['send_start_time'] = $kts;
                          $time_slot_arr['start_time'] = $vts;
                          $time_slot_arr['dining_slot_duration'] = $dining_slot_duration;
                          $reservable_time_slots[$kts] = $time_slot_arr;
                        }
                        else
                        {
                            $msg = trans('reservations.no_of_chairs_not_available');
                        }
                    }
                }
            }
            else
            {
                //calculate reserved seats for this($kts) slot
                $reserved_chairs = 0;
                $allowed_chair = 0;
                foreach ($reserved_time_range as $krtr => $vrtr) {
                    if($vrtr['due_time'] == $kts)
                    {
                        $reserved_chairs += $vrtr['reserved_chairs'];
                    }
                }
                //check if chairs are available for this($kts) slot
                foreach ($res_hrs_arr as $res_hr_key => $res_value) {
                    if(array_key_exists($kts, $res_value)){
                        $allowed_chair = ReservationHour::find($res_hr_key)->allowed_chair;
                    }
                }

                //check if seats are available for the slot, 
                if($allowed_chair > $reserved_chairs)
                {
                    //check if numb of requested seats by user are available
                    $available_chairs_for_slot = $allowed_chair - $reserved_chairs;
                    if($available_chairs_for_slot >= $requestArr['number_of_people'])
                    {
                        // $reservable_time_slots[$vts] = $kts;
                        $time_slot_arr = [];
                        $time_slot_arr['send_start_time'] = $kts;
                        $time_slot_arr['start_time'] = $vts;
                        $time_slot_arr['dining_slot_duration'] = ReservationHour::find($res_hr_key)->dining_slot_duration;
                        $reservable_time_slots[$kts] = $time_slot_arr;
                    }
                    else
                    {
                        $msg = trans('reservations.no_of_chairs_not_available');
                    }
                }

                
            }
        }
        $response['reservable_time_slots'] = $reservable_time_slots;
        $response['msg'] = $msg;
        // echo "<pre>";print_r($response);exit;
        return $response;
        
    }
}
