<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\RestaurantBranch;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\BusinessBranchStaff;
use App\Models\BusinessBranch;
use App\Models\SeatingArea;
use Auth;


class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:reservation-list', ['only' => ['index','show']]);
    }
    public function index()
    {
        $page_title = trans('reservations.heading');
        $reservations = Reservation::all();
        $user = Auth::user();
        return view('admin.reservations.index',compact('reservations','page_title','user'));
    }

    public function todaysReservations()
    {
        $page_title = trans('reservations.heading');
        $user = auth()->user();
        if($user->user_type == 'Manager') { 
          $reservations = Reservation::whereHas('restaurant_branch', function($q) use($user) {
            $q->where('manager_id', $user->id);
          })
          ->where('due_date',date('Y-m-d'))->get();
        } else { 
          $reservations = Reservation::where('due_date',date('Y-m-d'))->get();
        }
        // echo "<pre>";print_r($cuisines);exit;
        return view('admin.reservations.todays',compact('reservations','page_title'));
    }

    public function index_ajax(Request $request){
        $user = auth()->user();
        if($user->user_type == 'WaiterManager') { 
          $staff_branch = BusinessBranchStaff::where(['manage_reservations' => 1, 'staff_id' => $user->id])->first();
          $query = Reservation::where('business_branch_id', @$staff_branch->business_branch_id);
        }
        if($user->user_type == 'Vendor') {
          $query = Reservation::where('business_id', $user->business->id);
        }
        if($user->user_type == 'SuperAdmin') {
          $query = Reservation::query();
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
        $filter= $query;
        if($searchValue != ''){
            $filter = $filter->where(function($q)use ($searchValue) {
                            $q->whereHas('customer',function($query) use ($searchValue){
                                 $query->where('first_name','like','%'.$searchValue.'%');
                                        })
                            ->orWhereHas('restaurant',function($que) use ($searchValue){
                                 $que->whereHas('translation',function($qu) use ($searchValue){
                                 $qu->where('business_translations.brand_name','like','%'.$searchValue.'%');
                                        });
                              })
                            ->orWhereHas('restaurant_branch',function($que) use ($searchValue){
                                 $que->whereHas('translation',function($qu) use ($searchValue){
                                 $qu->where('business_branch_translations.branch_name','like','%'.$searchValue.'%');
                                        });
                              })
                            ->orWhere('reserved_chairs','like','%'.$searchValue.'%')
                            ->orWhere('check_in_date','like','%'.$searchValue.'%')
                            ->orWhere('created_at','like','%'.$searchValue.'%');
                        });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        
        $filter = $filter->orderBy($columnName, $columnSortOrder);
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
            ## Foreign Key Value
            if($user->user_type == 'SuperAdmin'){
              $emp['restaurant_name'] = $emp->restaurant->brand_name;
              $emp['restaurant_branch_name'] = $emp->restaurant_branch->branch_name;
              $emp['customer_name'] = $emp->customer->first_name;
            } else {
              $emp['restaurant_name'] = '<a href="'.route('businesses.show',$emp->restaurant->id).'">'.$emp->restaurant->brand_name.'</a>';
              $emp['restaurant_branch_name'] = '<a href="'.route('restaurant_branches.show',$emp->restaurant->id).'">'.$emp->restaurant_branch->branch_name.'</a>';
              $emp['customer_name'] = $emp->customer->first_name;
              
            }

            ## Set dynamic route for action buttons
            $emp['edit'] = route("reservations.edit",$emp["id"]);
            $emp['show'] = route("reservations.show",$emp["id"]);
            $emp['delete'] = route("reservations.destroy",$emp["id"]);
            $s_areas = $emp->seating_areas;
            $seating_areas = '';
            foreach ($s_areas as $sarea) {
              $seating_area = SeatingArea::find($sarea->stg_area_id); 
              $seating_areas .= @$seating_area->name.', ';
            }
            $seating_areas_data = rtrim(trim($seating_areas),',');
            $emp['seating_areas_data'] = ($seating_areas_data != '') ? $seating_areas_data : '-';
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

    public function index_ajax_today(Request $request){
        $user = auth()->user();
        if($user->user_type == 'Manager') { 
          $query = Reservation::whereHas('restaurant_branch', function($q) use ($user) {
              $q->where('manager_id',$user->id); 
          });
        } else {
          $query = Reservation::query();
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
        $filter= $query;
        if($searchValue != ''){
            $filter = $filter->where(function($q)use ($searchValue) {
                            $q->whereHas('customer',function($query) use ($searchValue){
                                 $query->where('first_name','like','%'.$searchValue.'%');
                                        })
                            ->orWhereHas('restaurant',function($que) use ($searchValue){
                                 $que->whereHas('translation',function($qu) use ($searchValue){
                                 $qu->where('restaurant_translations.name','like','%'.$searchValue.'%');
                                        });
                              })
                            ->orWhereHas('restaurant_branch',function($que) use ($searchValue){
                                 $que->whereHas('translation',function($qu) use ($searchValue){
                                 $qu->where('rest_branch_translations.name','like','%'.$searchValue.'%');
                                        });
                              })
                            ->orWhere('reserved_chairs','like','%'.$searchValue.'%')
                            ->orWhere('check_in_date','like','%'.$searchValue.'%')
                            ->orWhere('created_at','like','%'.$searchValue.'%');
                        });
        }
        $filter = $query;
        $filter = $filter->where('due_date',date('Y-m-d'));
        $totalRecordwithFilter = $filter->count();

        
        $filter = $filter->orderBy($columnName, $columnSortOrder);
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
            ## Foreign Key Value
            $emp['restaurant_name'] = '<a href="'.route('restaurants.show',$emp->restaurant->id).'">'.$emp->restaurant->name.'</a>';
            $emp['restaurant_branch_name'] = '<a href="'.route('restaurant_branches.show',$emp->restaurant->id).'">'.$emp->restaurant_branch->name.'</a>';
            $emp['customer_name'] = $emp->customer->first_name;

            ## Set dynamic route for action buttons
            $emp['edit'] = route("reservations.edit",$emp["id"]);
            $emp['show'] = route("reservations.show",$emp["id"]);
            $emp['delete'] = route("reservations.destroy",$emp["id"]);
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $page_title = trans('reservations.add_new');
        $restaurants = Restaurant::where('status','active')->get();
        $customers = User::where('status','active')->where('user_type','Customer')->get();
        return view('admin.reservations.create',compact('page_title','restaurants','customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $restBranch = RestaurantBranch::find($request->rest_branch_id);
        $validator= $request->validate([
            'customer_id' => 'required',
            'restaurant_id' => 'required',
            'rest_branch_id' => 'required',
            'reserved_chairs' => ['required',function ($attribute, $value, $fail) use($restBranch) {

                                    if ($value > $restBranch->available_seats) {
                                        $fail(trans('reservations.seats_not_available',['seats'=>$restBranch->available_seats]));
                                    }
                                }],
            'check_in_date' => 'required',

        ]);
        // if($request->reserved_chairs)
        $data = $request->all();
        if(Reservation::create($data)) {
            
            $restBranch->available_seats = $restBranch->available_seats - $request->reserved_chairs;
            $restBranch->save();
            return redirect()->route('reservations.index')->with('success',trans('reservations.added'));
        } else {
            return redirect()->route('reservations.index')->with('error',trans('reservations.error'));
        }
    }

    /**
     * Show the item for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = trans('reservations.update');
        $reservation = Reservation::find($id);

        $restaurants = Restaurant::where('status','active')->get();
        $customers = User::where('status','active')->where('user_type','Customer')->get();
        $rest_branches = RestaurantBranch::where('restaurant_id',$reservation->restaurant_id)->where('available_seats','>',0)->get();

        return view('admin.reservations.show',compact('page_title','reservation','restaurants','customers','rest_branches'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('reservations.update');
        $reservation = Reservation::find($id);

        $restaurants = Restaurant::where('status','active')->get();
        $customers = User::where('status','active')->where('user_type','Customer')->get();
        $rest_branches = RestaurantBranch::where('restaurant_id',$reservation->restaurant_id)->where('available_seats','>',0)->get();

        return view('admin.reservations.edit',compact('page_title','reservation','restaurants','customers','rest_branches'));
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
        $rest_branch = RestaurantBranch::find($request->rest_branch_id);
        $validator= $request->validate([
            'customer_id' => 'required',
            'restaurant_id' => 'required',
            'rest_branch_id' => 'required',
            'reserved_chairs' => ['required',function ($attribute, $value, $fail) use($rest_branch) {

                                    if ($value > $rest_branch->available_seats) {
                                        $fail(trans('reservations.seats_not_available',['seats'=>$rest_branch->available_seats]));
                                    }
                                }],
            'check_in_date' => 'required',
        ]);
        $data = $request->all();
        $reservation = Reservation::find($id);
        $restBranch = RestaurantBranch::find($reservation->rest_branch_id);
        $restBranch->available_seats = $restBranch->available_seats + $reservation->reserved_chairs;
        $restBranch->save();
        if($reservation->update($data)){
            $restB = RestaurantBranch::find($request->rest_branch_id);
            $restB->available_seats = $restB->available_seats - $request->reserved_chairs;
            $restB->save();
            return redirect()->route('reservations.index')->with('success',trans('reservations.updated'));
        } else {
            return redirect()->route('reservations.index')->with('error',trans('reservations.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reservation = Reservation::find($id);
        if($reservation->delete()){
            return redirect()->route('reservations.index')->with('success',trans('reservations.deleted'));
        }else{
            return redirect()->route('reservations.index')->with('error',trans('reservations.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request)
    {
        $res = Reservation::find($request->id);
        /*if($request->status == 'cancelled' || $request->status == 'checked_out')
        {
            $restBranch = RestaurantBranch::find($res->rest_branch_id);
            $restBranch->available_seats = $restBranch->available_seats + $res->reserved_chairs;
            $restBranch->save();
        }*/
        $user = Auth::user();
        $reservation = Reservation::where('id',$request->id)
               ->update(['status'=>$request->status]);
        if($user->user_type == 'WaiterManager'){
          $staff_branch = BusinessBranchStaff::where(['manage_reservations' => 1, 'staff_id' => $user->id])->first();
          $business_id = BusinessBranch::find(@$staff_branch->business_branch_id)->business_id;      
          $badge_count = Reservation::where(['business_id' => $business_id, 'status' => 'confirmed'])->count(); 
        } else {
          $badge_count = Reservation::where(['business_id' => Auth::user()->business->id, 'status' => 'confirmed'])->count();  
        }
       if($reservation) {
            return response()->json(['success' => trans('reservations.status_updated'), 'status' => $request->status, 'id' => $request->id, 'badge_count' => $badge_count]);
       } else {
            return response()->json(['error' => trans('reservations.error')]);
       }
    }

    public function getRestBranchesByRest(Request $request)
    {
        $states = RestaurantBranch::where('restaurant_id',$request->rest_id)->where('available_seats','>',0)->get();
        echo json_encode($states);
    }
}
   