<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CateringOrder;
use App\Models\RestaurantBranch;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\BusinessBranchStaff;
use App\Models\BusinessBranch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;


class CateringOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:catering-order-list', ['only' => ['index','show']]);
        // $this->middleware('permission:coupon-list', ['only' => ['index','show']]);
        // $this->middleware('permission:coupon-create', ['only' => ['create','store']]);
        // $this->middleware('permission:coupon-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('catering_orders.heading');
        return view('admin.catering_orders.index',compact('page_title'));
    }

    public function index_ajax(Request $request){

        $user = auth()->user();

        if($user->user_type == 'WaiterManager') { 
          $staff_branch = BusinessBranchStaff::where(['manage_catering_bookings' => 1, 'staff_id' => $user->id])->first();
          $query = CateringOrder::where('business_branch_id', @$staff_branch->business_branch_id)->where('order_status', '!=' , 'pending');
        } else {
          $query = CateringOrder::whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
            })->where('order_status', '!=' , 'pending');
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
                            ->orWhere('order_date','like','%'.$searchValue.'%');
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
          
            $emp['show'] = route("catering_orders.show",$emp["id"]);
            $date = \Carbon\Carbon::parse($emp['order_date']);
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
        $page_title = trans('catering_orders.show');
        $detail = CateringOrder::find($id);

        if(!$detail) {
          return redirect()->route('pickup_orders.index')->with('error',trans('common.no_data'));
        }

        return view('admin.catering_orders.show',compact('detail','page_title'));
    }

    public function one_view() {
      $page_title = trans('one_view.heading');
      $user       = Auth::user();

      if($user->user_type == 'WaiterManager') { 
          $staff_branch = BusinessBranchStaff::where(['manage_catering_bookings' => 1, 'staff_id' => $user->id])->first();
          $catering_orders = CateringOrder::where('business_branch_id', @$staff_branch->business_branch_id)->whereDate('due_date', '>=' ,date('Y-m-d'))->where('order_status', '!=' , 'pending')->latest()->paginate(12);
        } else {
          $catering_orders = CateringOrder::whereHas('restaurant_branch', function($q) use ($user){
                $q->where('business_id',$user->business->id);
            })->whereDate('due_date', '>=' ,date('Y-m-d'))->where('order_status', '!=' , 'pending')->latest()->paginate(12);
        }


      
      return view('admin.catering_orders.one_view',compact('page_title', 'catering_orders'));
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
        $catering_order = CateringOrder::where('id',$request->id)
               ->update(['order_status'=>$request->status]);


        $user = Auth::user();       
        if($user->user_type == 'WaiterManager'){
          $staff_branch = BusinessBranchStaff::where(['manage_reservations' => 1, 'staff_id' => $user->id])->first();
          $business_id = BusinessBranch::find(@$staff_branch->business_branch_id)->business_id;
          $badge_count = CateringOrder::whereHas('restaurant_branch', function($q) use ($vendor){
                $q->where('business_id',$business_id);
            })->whereDate('due_date', '>=' ,date('Y-m-d'))
            ->where('order_status', 'received')
            ->count();
        } else {
          $badge_count = CateringOrder::whereHas('restaurant_branch', function($q) use ($vendor){
                $q->where('business_id',$vendor->business->id);
            })->whereDate('due_date', '>=' ,date('Y-m-d'))
            ->where('order_status', 'received')
            ->count();
        }


       if($catering_order) {
            return response()->json(['success' => trans('reservations.status_updated'), 'order_status' => $request->status, 'id' => $request->id, 'badge_count' => $badge_count]);
       } else {
            return response()->json(['error' => trans('reservations.error')]);
       }
    }
}
   