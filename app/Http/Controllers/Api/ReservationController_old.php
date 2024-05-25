<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\SeatingArea;
use App\Models\ReservationSeatingArea;
use App\Models\RestaurantBranch;
use App\Models\RestaurantWorkingHour;
use App\Models\Reservation;
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
    * AVAILABLE_TIMES OLD
    *
    * @return JSON
    */
	  public function available_times(Request $request) 
    {
        //VALIDATION ..
        $validator=  Validator::make($request->all(),[
            'restaurant_branch_id'   => 'required|numeric',
            'date'   => 'required|date|date_format:Y-m-d',
            'number_of_people'   => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendValidationError('', $validator->errors()->first());
        }

        if(date('Y-m-d') > $request->date) {
            return $this->sendError('',trans('restaurants.its_old_date'));
        }

    	  $rest_branch = RestaurantBranch::where(['id' => $request->restaurant_branch_id, 'status' => 'active'])->first();
        if(!$rest_branch) {
            return $this->sendError('',trans('restaurants.not_found')); 
        }

        if($request->number_of_people > $rest_branch->total_seats) {
            return $this->sendError('',trans('reservations.invalid_no_of_people'));
        }

        // SEATS
        $total_seats = $rest_branch->total_seats;

        // DATE RESERVABLE ?
        $weekday = date('N', strtotime($request->date));
        $working_days = RestaurantWorkingHour::where('restaurant_id', $rest_branch->restaurant_id)->first();
        if(!$working_days) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }
        $working_days_array = $this->get_working_days($working_days->from_day, $working_days->to_day);

        if(!in_array($weekday, $working_days_array)) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }

        //RESTAURANT IS AVAILABLE ?
        $restaurant = Restaurant::where(['id' => $rest_branch->restaurant_id, 'status' => 'active'])->first();
        if(!$restaurant) {
          return $this->sendError('',trans('restaurants.not_found')); 
        }

        if($request->date == date('Y-m-d') && $restaurant->working_status != 'available') {
          return $this->sendError('',trans('restaurants.bookings_disabled')); 
        }

        $requestArr = $request->all();
        $reservable_slots = $this->getAvailableTimeSlots($working_days,$total_seats,$requestArr);
        // echo "<pre>";print_r($reservable_slots);exit;
        $reservable_time_slots_p1 = [];
        foreach ($reservable_slots as $kres => $vres) {
            $res_time = [];
            $res_time['time'] = $kres;
            $res_time['send_time'] = $vres;
            array_push($reservable_time_slots_p1, $res_time);
        }
        // echo "<pre>";print_r($reservable_slots);exit;
        // ***** GETTING GAPS OF TIMESLOTS WHICH NOT FITS IN EAT-TIME
        // adding start and end day gaps
        /*if(count($reserved_time_range)) {
            $starts = ['due_time' => '00:00:00', 'end_time' => $working_days->from_time];
                  
            $ends = [ 'due_time' => date("H:i:s", strtotime($reserved_time_range[count($reserved_time_range)-1]['due_time']) + ($eating_gape * 60)), 
                        'end_time' => $working_days->to_time
                      ];
            array_unshift($reserved_time_range, $starts);
            $reserved_time_range[] = $ends;
        }
        // return $reserved_time_range;
        $gap_slots = [];
        for($r=0; $r < count($reserved_time_range); $r++) {
            if($r < count($reserved_time_range)-1){
                $gslots = [];    
                $gslots = $this->splitTimeCore($request->date.' '.date("H:i:s", strtotime($reserved_time_range[$r]['end_time']) + ($eating_gape * 60)), $request->date.' '.$reserved_time_range[$r+1]['due_time'], "05", $eating_gape);
                  
                foreach ($gslots as $gsk => $gsv) {
                    $gap_slots[$gsk] = $gsv;             
                } 
            }
        }*/
        // **** GETTING RESERVABLE TIME SLOTS
        /*  $reservable_time_slots = [];
            foreach($reservable_time_slots_p1 as $krtsp=>$rtspv)
            {
                if(!array_key_exists($krtsp, $gap_slots)){
                    $reservable_time_slots[$krtsp] = $rtspv;  
                }
            }
        */

        if($reservable_time_slots_p1)
        {        
            return $this->sendResponse($reservable_time_slots_p1, trans('reservations.available_slots'));
        }
        else
        {
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
        $reservation = Reservation::where('rest_branch_id',$request->restaurant_branch_id)
                ->where('customer_id',Auth::guard('api')->user()->id)
                ->where('due_date',$request->date)
                ->where('due_time',$request->time)
                ->first();

        if($reservation)
        {
            return $this->sendError('', trans('reservations.already_reserved',['date' => $request->date,'time' => date('h:i A',strtotime($request->time))]));
        }

        $validator= $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required',
            'number_of_people' => 'required|numeric',
            'restaurant_branch_id' => 'required|numeric',

        ]);
        
        //vallidate restaurant timings
        if(date('Y-m-d') > $request->date) {
            return $this->sendError('',trans('restaurants.its_old_date'));
        }

        $restBranch = RestaurantBranch::where(['id' => $request->restaurant_branch_id, 'status' => 'active'])->first();
        if(!$restBranch) {
            return $this->sendError('',trans('restaurants.not_found')); 
        }

        if($request->number_of_people > $restBranch->total_seats) {
            return $this->sendError('',trans('reservations.invalid_no_of_people'));
        }

        // SEATS
        $total_seats = $restBranch->total_seats;

        // DATE RESERVABLE ?
        $weekday = date('N', strtotime($request->date));
        $working_days = RestaurantWorkingHour::where('restaurant_id', $restBranch->restaurant_id)->first();
        if(!$working_days) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }
        $working_days_array = $this->get_working_days($working_days->from_day, $working_days->to_day);

        if(!in_array($weekday, $working_days_array)) {
          return $this->sendError('',trans('reservations.non_working_day'));
        }

        //RESTAURANT IS AVAILABLE ?
        $restaurant = Restaurant::where(['id' => $restBranch->restaurant_id, 'status' => 'active'])->first();
        if(!$restaurant) {
          return $this->sendError('',trans('restaurants.not_found')); 
        }

        if($request->date == date('Y-m-d') && $restaurant->working_status != 'available') {
          return $this->sendError('',trans('restaurants.bookings_disabled')); 
        }

        $requestArr = $request->all();
        $reservable_slots = $this->getAvailableTimeSlots($working_days,$total_seats,$requestArr);
        if(!in_array($request->time, $reservable_slots))
        {
            return $this->sendError('',trans('reservations.slots_not_available')); 
        }
        $data = [
            'customer_id' => Auth::guard('api')->user()->id,
            'restaurant_id' => $restBranch->restaurant_id,
            'rest_branch_id' => $restBranch->id,
            'reserved_chairs' => $request->number_of_people,
            'check_in_date' => $request->date.' '.$request->time,
            'due_date' => $request->date,
            'due_time' => $request->time,
            'end_time' => date("H:i", strtotime('+'.Setting::get('Dining_Slot_Duration').' minutes', strtotime($request->time)))
        ];
        
        if($res = Reservation::create($data)) {
            foreach ($stg_areas as $vstr) {
                ReservationSeatingArea::create(['reservation_id' => $res->id, 'stg_area_id' => $vstr->id]);
            }

            $restBranch->available_seats = $restBranch->available_seats - $request->number_of_people;
            $restBranch->save();

            return $this->sendResponse(new ReservationResource($res), trans('reservations.added'));
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

    public function getAvailableTimeSlots($working_days,$total_seats,$requestArr)
    {
        // *** TIME SLOTS RANGE STARTS ** // 
        $eating_gape = Setting::get('Dining_Slot_Duration');
        $time_slots_gaps = 45;

        // ***GETTING ALL WORKING HOURS
        $rbranch_working_from = $working_days->from_time;
        $rbranch_working_to = date("H:i:s", strtotime($working_days->to_time) - ($eating_gape * 60));

        $time_slots = $this->splitTime($requestArr['date'].' '.$rbranch_working_from, $requestArr['date'].' '.$rbranch_working_to, $time_slots_gaps);

        $reserved_time_range = Reservation::where([
                                      'rest_branch_id' => $requestArr['restaurant_branch_id'],
                                      'due_date' => $requestArr['date'],
                                  ])
                                  ->whereIn('status', ['reserved', 'checked_in'])
                                  ->select(['due_time', 'end_time', 'reserved_chairs'])
                                  ->orderBy('check_in_date')
                                  ->get()
                                  ->toArray();

        $reserved_time_slots = [];
        foreach ($reserved_time_range as $rte) {
            $slots = []; 
            $slots =  $this->splitTimeRezerved($requestArr['date'].' '.$rte['due_time'], $requestArr['date'].' '.$rte['end_time'], "$time_slots_gaps");
            foreach ($slots as $s => $v) {
                $reserved_time_slots[$s] = $v;             
            }
        }

        $reservable_time_slots_p1 = [];
        foreach($time_slots as $kts=>$vts)
        {
            if(!array_key_exists($kts, $reserved_time_slots)){
                /*$res_time = [];
                $res_time['time'] = $vts;
                $res_time['send_time'] = $kts;
                array_push($reservable_time_slots_p1, $res_time); */
                $reservable_time_slots_p1[$vts] = $kts;
            }
            else
            {
                //calculate reserved seats for the slot which is reserved
                $reserved_chairs = 0;
                foreach ($reserved_time_range as $krtr => $vrtr) {
                    if($vrtr['due_time'] == $kts)
                    {
                        $reserved_chairs += $vrtr['reserved_chairs'];
                    }
                }

                // check if seat is available for already reserved slot
                if($total_seats > $reserved_chairs)
                {
                    //if seats are available for the slot, check if numb of requested seats by user is available
                    $available_seats_for_slot = $total_seats - $reserved_chairs;
                    if($available_seats_for_slot >= $requestArr['number_of_people'])
                    {
                        /*$res_time = [];
                        $res_time['time'] = $vts;
                        $res_time['send_time'] = $kts;*/
                        $reservable_time_slots_p1[$vts] = $kts;
                        // array_push($reservable_time_slots_p1, $res_time);
                    }
                }
            }
        }
        return $reservable_time_slots_p1;
        
    }
}
