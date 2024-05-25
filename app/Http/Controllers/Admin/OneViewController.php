<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\RestaurantBranch;
use App\Models\Restaurant;


use App\Models\BusinessBranch;
use App\Models\WaitingList;
use App\Models\PickupOrder;
use App\Models\BusinessBranchStaff;


use App\Models\User;
use Auth;


class OneViewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:one-view', ['only' => ['one_view','one_view_detail']]);
    }


    public function one_view($type = '')
    {
        $page_title = trans('one_view.heading');
        $user       = Auth::user();

        if($user->user_type == 'Vendor'){
          $business_id = $user->business->id;
        }
        if($user->user_type == 'WaiterManager'){
          $business_id = $user->business_branch_staff->business_branch->business_id;
        }

        if($user->user_type == 'Vendor'){
          // echo $business_id; die;
          $reservation_button = BusinessBranch::where(['branch_type' => 'restaurant', 'business_id' => $business_id, 'reservation_allow' => 1])->count();
          $waiting_list_button = BusinessBranch::where(['branch_type' => 'restaurant', 'business_id' => $business_id, 'waiting_list_allow' => 1])->count();
          $pickup_button = BusinessBranch::where(['branch_type' => 'restaurant', 'business_id' => $business_id, 'pickup_allow' => 1])->count();
        } else { // Waiter
          $reservation_button = BusinessBranchStaff::where(['manage_reservations' => 1, 'staff_id' => $user->id])->count();
          $waiting_list_button = BusinessBranchStaff::where(['manage_waiting_list' => 1, 'staff_id' => $user->id])->count();
          $pickup_button = BusinessBranchStaff::where(['manage_pickups' => 1, 'staff_id' => $user->id])->count();
        }
        // print_r($reservation_button); print_r($waiting_list_button); print_r($pickup_button); die;
        

        if($type == 'reservations') {
          if($user->user_type == 'Vendor') {
            $reservations = Reservation::where('business_id', $user->business->id)
              ->whereDate('due_date', '>=' ,date('Y-m-d'))->latest()->paginate(12);
            $badge_count = Reservation::where(['business_id' => Auth::user()->business->id, 'status' => 'confirmed'])->whereDate('due_date', '>=' ,date('Y-m-d'))->count();   
          } else { //waiter
            $staff_branch = BusinessBranchStaff::where(['manage_reservations' => 1, 'staff_id' => $user->id])->first();
            $reservations = Reservation::where('business_branch_id', @$staff_branch->business_branch_id)
              ->whereDate('due_date', '>=' ,date('Y-m-d'))->latest()->paginate(12);
            $badge_count = Reservation::where(['business_branch_id' => @$staff_branch->business_branch_id, 'status' => 'confirmed'])->whereDate('due_date', '>=' ,date('Y-m-d'))->count(); 
          }
          return view('admin.one_view.reservations',compact('reservations','page_title', 'badge_count', 'reservation_button', 'waiting_list_button', 'pickup_button'));
          
        }

        if($type == 'waiting_list') {
          if($user->user_type == 'Vendor') {
            $waiting_list = WaitingList::where('business_id', $user->business->id)
              ->whereDate('wl_date' ,date('Y-m-d'))->latest()->paginate(12);
            $badge_count = WaitingList::where(['business_id' => $user->business->id, 'status' => 'in_queue'])->whereDate('wl_date' ,date('Y-m-d'))->count();  
          } else {
            $staff_branch = BusinessBranchStaff::where(['manage_waiting_list' => 1, 'staff_id' => $user->id])->first();
            $waiting_list = WaitingList::where('business_branch_id', @$staff_branch->business_branch_id)
              ->whereDate('wl_date' ,date('Y-m-d'))->latest()->paginate(12);
            $badge_count = WaitingList::where(['business_branch_id' => @$staff_branch->business_branch_id, 'status' => 'in_queue'])->whereDate('wl_date' ,date('Y-m-d'))->count(); 
          }
          return view('admin.one_view.waiting_list',compact('waiting_list','page_title', 'badge_count', 'reservation_button', 'waiting_list_button', 'pickup_button'));
          
        }

        if($type == 'pickups') {
          if($user->user_type == 'Vendor') {
            $upcoming_pickups = PickupOrder::whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
            })->whereDate('due_date' ,'>=',date('Y-m-d'))
            ->whereIn('order_status', ['received', 'confirmed', 'cancelled'])
            ->latest()->paginate(12);

            $pickups_in_progress = PickupOrder::whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
            })->whereDate('due_date',date('Y-m-d'))
            ->where('order_status', 'confirmed', 'cancelled')
            ->get();

            $pickups_ready = PickupOrder::whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
            })->whereDate('due_date',date('Y-m-d'))
            ->where('order_status', 'ready_for_pickup', 'cancelled')
            ->get();

            $badge_count = PickupOrder::whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
            })->whereDate('due_date' ,'>=',date('Y-m-d'))
            ->where('order_status', 'received')
            ->count();
          } else {
            $staff_branch = BusinessBranchStaff::where(['manage_pickups' => 1, 'staff_id' => $user->id])->first();
            $upcoming_pickups = PickupOrder::where('business_branch_id', @$staff_branch->business_branch_id)->whereDate('due_date' ,'>=',date('Y-m-d'))
            ->whereIn('order_status', ['received', 'confirmed', 'cancelled'])
            ->latest()->paginate(12);

            $pickups_in_progress = PickupOrder::where('business_branch_id', @$staff_branch->business_branch_id)->whereDate('due_date',date('Y-m-d'))
            ->where('order_status', 'confirmed', 'cancelled')
            ->get();

            $pickups_ready = PickupOrder::where('business_branch_id', @$staff_branch->business_branch_id)->whereDate('due_date',date('Y-m-d'))
            ->where('order_status', 'ready_for_pickup', 'cancelled')
            ->get();

            $badge_count = PickupOrder::where('business_branch_id', @$staff_branch->business_branch_id)->whereDate('due_date' ,'>=',date('Y-m-d'))
            ->where('order_status', 'received')
            ->count();
          }  
          return view('admin.one_view.pickups',compact('upcoming_pickups','page_title', 'badge_count', 'pickups_in_progress', 'pickups_ready', 'reservation_button', 'waiting_list_button', 'pickup_button'));
        }
    }

    public function one_view_detail($type, $id) {
        $page_title = trans('one_view.heading');
        $user       = Auth::user();

        if($user->user_type == 'Vendor') {
          $reservation_button = BusinessBranch::where(['branch_type' => 'restaurant', 'business_id' => $user->business->id, 'reservation_allow' => 1])->count();
          $waiting_list_button = BusinessBranch::where(['branch_type' => 'restaurant', 'business_id' => $user->business->id, 'waiting_list_allow' => 1])->count();
          $pickup_button = BusinessBranch::where(['branch_type' => 'restaurant', 'business_id' => $user->business->id, 'pickup_allow' => 1])->count();
        } else { //waiter
          $reservation_button = BusinessBranchStaff::where(['manage_reservations' => 1, 'staff_id' => $user->id])->count();
          $waiting_list_button = BusinessBranchStaff::where(['manage_waiting_list' => 1, 'staff_id' => $user->id])->count();
          $pickup_button = BusinessBranchStaff::where(['manage_pickups' => 1, 'staff_id' => $user->id])->count();
        }

        if($type == 'reservation') {
          if($user->user_type == 'Vendor') {
            $reservation = Reservation::where(['id' => $id, 'business_id' => $user->business->id])->first();
          }else{
            $staff_branch = BusinessBranchStaff::where(['manage_reservations' => 1, 'staff_id' => $user->id])->first();
            $reservation = Reservation::where(['id' => $id, 'business_branch_id' => @$staff_branch->business_branch_id])->first();
          }
          if($reservation) {
            if($user->user_type == 'Vendor') {
              $badge_count = Reservation::where(['business_id' => Auth::user()->business->id, 'status' => 'confirmed'])->whereDate('due_date', '>=' ,date('Y-m-d'))->count();  
            } else {
              $badge_count = Reservation::where(['business_branch_id' => @$staff_branch->business_branch_id, 'status' => 'confirmed'])->whereDate('due_date', '>=' ,date('Y-m-d'))->count();
            } 
            return view('admin.one_view.reservation_detail',compact('reservation','page_title', 'badge_count', 'reservation_button', 'waiting_list_button', 'pickup_button'));
          }else{
              return redirect()->route('one_view','reservations');
          }
        } else if($type == 'waiting_list') {
          if($user->user_type == 'Vendor') {
            $waiting_list = WaitingList::where(['id' => $id, 'business_id' => $user->business->id])->first();
          
          }else{
            $staff_branch = BusinessBranchStaff::where(['manage_waiting_list' => 1, 'staff_id' => $user->id])->first();
            $waiting_list = WaitingList::where(['id' => $id, 'business_branch_id' => @$staff_branch->business_branch_id])->first();
          }
          if($waiting_list) {
            if($user->user_type == 'Vendor') {
              $badge_count = WaitingList::where(['business_id' => $user->business->id, 'status' => 'in_queue'])->whereDate('wl_date' ,date('Y-m-d'))->count();  
            } else {
              $badge_count = WaitingList::where(['business_branch_id' => @$staff_branch->business_branch_id, 'status' => 'in_queue'])->whereDate('wl_date' ,date('Y-m-d'))->count(); 
            }  
            return view('admin.one_view.waiting_list_detail',compact('waiting_list','page_title', 'badge_count', 'reservation_button', 'waiting_list_button', 'pickup_button'));
          }else{
              return redirect()->route('one_view','waiting_list');
          }
        } else if($type == 'pickups') {
          if($user->user_type == 'Vendor') {
            $pickup = PickupOrder::where(['id' => $id])->first();
          } else {
            $staff_branch = BusinessBranchStaff::where(['manage_pickups' => 1, 'staff_id' => $user->id])->first();
            $pickup = PickupOrder::where(['id' => $id, 'business_branch_id' => @$staff_branch->business_branch_id])->first();
          }
          if($pickup) {
            if($user->user_type == 'Vendor') {
              $badge_count = PickupOrder::whereHas('restaurant_branch', function($q) use ($user){
                      $q->where('business_id',$user->business->id);
                  })->whereDate('due_date' ,'>=',date('Y-m-d'))
                  ->where('order_status', 'received')
                  ->count();  
            } else {
              $badge_count = PickupOrder::where(['business_branch_id' => @$staff_branch->business_branch_id, 'order_status' => 'received'])->whereDate('due_date' ,'>=',date('Y-m-d'))
                  ->count();
            }   
            return view('admin.one_view.pickup_detail',compact('pickup','page_title', 'badge_count', 'reservation_button', 'waiting_list_button', 'pickup_button'));
          }else{
              return redirect()->route('one_view','pickup');
          }
        }
        else{
            return redirect()->route('one_view','reservations');
        }
    }

    public function index()
    {
            $page_title = trans('one_view.heading');
            $user       = Auth::user();

            $reservations = Reservation::where('business_id', $user->business->id)
              ->whereDate('due_date', '>=' ,date('Y-m-d'))->limit('10')->get();
            return view('admin.one_view.index',compact('reservations','page_title'));
        }

   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

}
   