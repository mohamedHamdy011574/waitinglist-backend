<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaitingList;
use App\Models\RestaurantBranch;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\BusinessBranchStaff;
use App\Models\BusinessBranch;
use Auth;
use App\Models\Helpers\CommonHelpers;
use Validator;
use Carbon\Carbon;
use DB;


class WaitingListController extends Controller
{
    use CommonHelpers;

    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:waiting-list', ['only' => ['index','show']]);
        $this->middleware('permission:waiting-list-create', ['only' => ['create']]);
    }

    public function index()
    {
        $page_title = trans('waiting_list.heading');
        return view('admin.waiting_list.index',compact('page_title'));
    }

     public function index_ajax(Request $request){

        $user = auth()->user();

        if($user->user_type == 'WaiterManager') { 
          $staff_branch = BusinessBranchStaff::where(['manage_waiting_list' => 1, 'staff_id' => $user->id])->first();
          $query = WaitingList::where('business_branch_id', @$staff_branch->business_branch_id);
        } else {
          $query = WaitingList::where('business_id',$user->business->id);
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
                            ->orWhere('wl_datetime','like','%'.$searchValue.'%');
                        });

            $user = auth()->user();

            if($user->user_type == 'WaiterManager') { 

              $filter = $filter->whereHas('branch_staff', function($q) use ($user) {
                  $q->where('staff_id',$user->id); 
              });

            }else {

              $filter = $filter->where('business_id',$user->business->id);

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
          
            $emp['show'] = route("waiting_list.show",$emp["id"]);
            $date = \Carbon\Carbon::parse($emp['wl_datetime']);
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $page_title = trans('waiting_list.add_new');
       
        return view('admin.waiting_list.create',compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $request->validate([

            'first_name'      => 'required|min:1|max:100',
            'phone_number'    => 'required|max:13',
            // 'wl_datetime'     => 'required',
            'reserved_chairs' => 'required|numeric|min:1',
            
        ]);

        DB::beginTransaction();

        $user = auth()->user();

        if($user->user_type == 'WaiterManager') { 
          $staff_branch = BusinessBranchStaff::where(['manage_waiting_list' => 1, 'staff_id' => $user->id])->first();
          $business_id = BusinessBranch::where('id', @$staff_branch->business_branch_id)->first()->business_id;
          $business_branch_id = @$staff_branch->business_branch_id;
        } else {
          $business_id = $user->business->id;
          $business_branch_id = null;
        }
        // print_r($business_branch_id); die;


        //CHECING CRITERAS
          $data = $request->all();
          $data['date'] = date('Y-m-d');
          $rest_branch = BusinessBranch::find($business_branch_id);
          // IF WAITING LIST ALLOW ?
            if($rest_branch->waiting_list_allow == 0){
              return redirect()->back()->withInput()->with('error', trans('business_branches.no_waiting_list_service'));
            }

            //IS THIS WORKING DAY?
            // $weekday = date('D', strtotime('2020-10-24'));
            // echo $weekday; die;
            $weekday = date('D', strtotime($data['date']));
            // echo $rest_branch->business->working_hours; die;
            if($weekday == 'Sun' && @$rest_branch->business->working_hours->sunday_serving == 0) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_day'));
            }
            if($weekday == 'Mon' && @$rest_branch->business->working_hours->monday_serving == 0) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_day'));
            }
            if($weekday == 'Tue' && @$rest_branch->business->working_hours->tuesday_serving == 0) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_day'));
            }

            if($weekday == 'Wed' && @$rest_branch->business->working_hours->wednesday_serving == 0) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_day'));
            }

            // echo $rest_branch->business->working_hours->thursday_serving;
            if($weekday == 'Thu' && @$rest_branch->business->working_hours->thursday_serving == 0) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_day'));
            }
            if($weekday == 'Fri' && @$rest_branch->business->working_hours->friday_serving == 0) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_day'));
            }
            if($weekday == 'Sat' && @$rest_branch->business->working_hours->saturday_serving == 0) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_day'));
            }
            // echo $weekday; die;

            //CHEKING WORKING HOURS RANGE
            $from_time = $rest_branch->business->working_hours->from_time;
            $to_time = $rest_branch->business->working_hours->to_time;
            $current_time = date('H:i:s');
            // echo $from_time.'<br>';echo $to_time.'<br>';echo $current_time.'<br>';


            $is_ok =  Carbon::now()->between(
                Carbon::parse($from_time), 
                Carbon::parse($to_time)
            );
            if(!$is_ok) {
                return redirect()->back()->withInput()->with('error', trans('business_branches.no_working_hours'));
            }



            //CHECKING ALLOWED CHAIRS
            $reservation_hours = $rest_branch->business->reservation_hours;
            // echo '<pre>'; print_r($reservation_hours); die;
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
            // echo '<br>'.$allowed_chairs; die('ddd');

            if($allowed_chairs < $data['reserved_chairs']) { // 20 < 5
                if($allowed_chairs > 0){
                    return redirect()->back()->withInput()->with('error', trans('business_branches.reserved_chairs_invalid',['allowed_chairs' => $allowed_chairs]));
                }else{
                    return redirect()->back()->withInput()->with('error', trans('business_branches.restaurant_closed_now'));
                }
            }
        //CHECING CRITERAS

        // GETTING TOKEN NUMBER
        $last_token = WaitingList::where([
                'business_branch_id' => $business_branch_id,
                'wl_date' => Carbon::now()->format('Y-m-d'),
            ])->orderBy('created_at','DESC')->first();
        if($last_token) {
           $token = ($last_token->token_number + 1);  
        } else {
           $token = 1; 
        }


        $booking               = new WaitingList();
        $booking->first_name   = $request->first_name;
        $booking->phone_number = $request->phone_number;
        $booking->token_number = $token;
        $booking->business_id = $business_id;
        $booking->business_branch_id = $business_branch_id;
        $booking->reserved_chairs = $request->reserved_chairs;
        $booking->wl_datetime     = Carbon::now();
        $booking->wl_date     = date('Y-m-d');
        $booking->wl_time     = date('H:i:s');
        


        if($booking->save()) {

            DB::commit();
            return redirect()->route('waiting_list.index')->with('success',trans('waiting_list.added'));

        } else {

            DB::rollback();
            return redirect()->route('waiting_list.index')->with('error',trans('waiting_list.error'));
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = trans('waiting_list.show');
        $detail = WaitingList::find($id);

        if(!$detail) {
          return redirect()->route('waiting_list.index')->with('error',trans('common.no_data'));
        }

        return view('admin.waiting_list.show',compact('detail','page_title'));
    }

     /**
     * Send Push Notification
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_notification(Request $request)
    {

        $validator = Validator::make($request->all(),[

            'id'           => 'required|exists:waiting_list,id',
            'title'        => 'required|max:50',
            'body'         => 'required|max:300'

        ]);
      
        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first(),'type'=>'error']);
        }

        $waiting_list = WaitingList::find($request->id);

        if($waiting_list) {
          if($waiting_list->wl_date != date('Y-m-d') || $waiting_list->status != 'in_queue') {
            return response()->json(['message' => trans('waiting_list.not_allowed_notification'),'type'=>'error']);
          }  

          $user = User::where(['id'=>$waiting_list->customer_id])->first();
          if($user == null){
            return response()->json(['message' => trans('waiting_list.user'),'type'=>'error']);
          }
          $this->sendNotification($user,$request->title,$request->body,"waiting_list",$waiting_list->business_id);
          return response()->json(['message' => trans('waiting_list.sent'),'type'=>'success']);
        } else {
          return response()->json(['message' => trans('waiting_list.error'),'type'=>'error']);
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

        $reservation = WaitingList::where('id',$request->id)
               ->update(['status'=>$request->status]);
               
        $user = Auth::user();       
        if($user->user_type == 'WaiterManager'){
          $staff_branch = BusinessBranchStaff::where(['manage_waiting_list' => 1, 'staff_id' => $user->id])->first();
          $business_id = BusinessBranch::find(@$staff_branch->business_branch_id)->business_id;
          $badge_count = WaitingList::where(['business_id' => $business_id, 'status' => 'in_queue'])->count();  
        }  else {
          $badge_count = WaitingList::where(['business_id' => Auth::user()->business->id, 'status' => 'in_queue'])->count();  
        }    

       if($reservation) {
            return response()->json(['success' => trans('waiting_list.status_updated'), 'status' => $request->status, 'id' => $request->id, 'badge_count' => $badge_count]);
       } else {
            return response()->json(['error' => trans('waiting_list.error')]);
       }
    }

}
   