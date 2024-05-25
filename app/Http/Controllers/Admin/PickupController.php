<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PickupOrder;
use App\Models\RestaurantBranch;
use App\Models\BusinessBranch;
use App\Models\BusinessBranchStaff;
use App\Models\Restaurant;
use App\Models\User;
use Auth;
use App\Models\Helpers\CommonHelpers;


class PickupController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:pickups-order-list', ['only' => ['index','show']]);
        // $this->middleware('permission:coupon-list', ['only' => ['index','show']]);
        // $this->middleware('permission:coupon-create', ['only' => ['create','store']]);
        // $this->middleware('permission:coupon-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('pickups.heading');
        return view('admin.pickups.index',compact('page_title'));
    }

    public function index_ajax(Request $request){

        $user = auth()->user();

        if($user->user_type == 'WaiterManager') { 
          $staff_branch = BusinessBranchStaff::where(['manage_pickups' => 1, 'staff_id' => $user->id])->first();
          $query = PickupOrder::where('business_branch_id', @$staff_branch->business_branch_id);
        } else {
          $query = PickupOrder::whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
            });
        }
        ## Total number of records without filtering
        $totalRecords = $query->count();

        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $length = ($request['length'] == -1) ? $totalRecords : $request['length']; 
        $rowperpage      =    $length; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        ## Total number of record with filtering
        $filter = $query;
        if($searchValue != ''){

            $filter = $filter->where(function($q)use ($searchValue) {
                            
                            $q->where('first_name','like','%'.$searchValue.'%')
                            ->orWhere('phone_number','like','%'.$searchValue.'%')
                            ->orWhere('pickup_date','like','%'.$searchValue.'%');
                        });

            $user = auth()->user();

            if($user->user_type == 'WaiterManager') { 

              $filter = $filter->whereHas('branch_staff', function($q) use ($user) {
                  $q->where('staff_id',$user->id); 
              });

            }else {

              $filter = $filter->whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
              });

            }
        }

        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        
        $filter = $filter->orderBy($columnName, $columnSortOrder);
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
           

            ## Set dynamic route for action buttons
          
            $emp['show'] = route("pickup_orders.show",$emp["id"]);
            $date = \Carbon\Carbon::parse($emp['pickup_date']);
            $emp['date'] = $date->format('d M, Y  h:i A');
           
            $data[]      = $emp;
        }

        ## Response
        $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecords,
          "iTotalDisplayRecords" => $totalRecordwithFilter,
          "aaData" => $data
        );

        echo json_encode($response);
    }
    

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = trans('pickups.show');
        $detail = PickupOrder::find($id);

        if(!$detail) {
          return redirect()->route('pickup_orders.index')->with('error',trans('common.no_data'));
        }

        return view('admin.pickups.show',compact('detail','page_title'));
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request)
    {

        $vendor = Auth::user();
        $pickups = PickupOrder::find($request->id);
        if(!$pickups) {
          return response()->json(['error' => trans('common.no_data')]);
        }

        if($pickups->due_date != date('Y-m-d') || $pickups->order_status == 'picked_up') {
            return response()->json(['success' => trans('pickups.not_allowed_notification_or_status')]);
        }
        $pickups->update(['order_status'=>$request->status]);
        //notification
        $business_branch = BusinessBranch::find($pickups->business_branch_id);
        $customer = User::find($pickups->customer_id);
        $title = $body = '';  
        if($pickups->order_status == 'received') {
          $title = trans('pickups.status_for_api.received');
          $body = trans('pickups.status_for_api.received_message');
        }
        if($pickups->order_status == 'confirmed') {
          $title = trans('pickups.status_for_api.confirmed');
          $body = trans('pickups.status_for_api.confirmed_message');
        }
        if($pickups->order_status == 'ready_for_pickup') {
          $title = trans('pickups.status_for_api.ready_for_pickup');
          $body = trans('pickups.status_for_api.ready_for_pickup_message');
        }
        if($pickups->order_status == 'picked_up') {
          $title = trans('pickups.status_for_api.picked_up');
          $body = trans('pickups.status_for_api.picked_up_message');
        }
        if($pickups->order_status == 'cancelled') {
          $title = trans('pickups.status_for_api.cancelled');
          $body = trans('pickups.status_for_api.cancelled_message');
        }
        if($customer) {
          $this->sendNotification($customer,$title,$body,"order_status",$business_branch->business_id);
        }

        $user = Auth::user();       
        if($user->user_type == 'WaiterManager'){
          $staff_branch = BusinessBranchStaff::where(['manage_pickups' => 1, 'staff_id' => $user->id])->first();
          $business_id = BusinessBranch::find(@$staff_branch->business_branch_id)->business_id;
          $badge_count = PickupOrder::whereHas('restaurant_branch', function($q) use ($vendor, $business_id){
                $q->where('business_id',$business_id);
            })->whereDate('due_date', '>=' ,date('Y-m-d'))
            ->where('order_status', 'received')
            ->count();
        }  else {       
          $badge_count = PickupOrder::whereHas('restaurant_branch', function($q) use ($vendor){
                $q->where('business_id',$vendor->business->id);
            })->whereDate('due_date', '>=' ,date('Y-m-d'))
            ->where('order_status', 'received')
            ->count();
        }


       if($pickups) {
            return response()->json(['success' => trans('reservations.status_updated'), 'order_status' => $request->status, 'id' => $request->id, 'badge_count' => $badge_count]);
       } else {
            return response()->json(['error' => trans('reservations.error')]);
       }
    }
}
   