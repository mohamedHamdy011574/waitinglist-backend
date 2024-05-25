<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use App\Models\BusinessBranch;
use App\Models\BusinessBranchStaff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Auth;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:staff-list', ['only' => ['index','show']]);
        $this->middleware('permission:staff-create', ['only' => ['create','store']]);
        $this->middleware('permission:staff-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('staff.heading');
        // $staff = User::where('user_type','WaiterManager')->get();
        return view('admin.staff.index',compact('page_title'));
    }

    public function index_ajax(Request $request){
        $query = User::where(['user_type' => 'WaiterManager', 'vendor_id' => Auth::user()->id]);
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
          // echo "<pre>";print_r($searchValue);exit;
        ## Total number of record with filtering
        $filter= $query;
        if($searchValue != ''){
            $filter = $filter->where(function($q)use ($searchValue) {
                          $q->WhereHas('business',function($que) use ($searchValue)  {
                                 $que->whereHas('translation',function($qu) use ($searchValue){
                                 $qu->where('brand_name','like','%'.$searchValue.'%');
                                        });
                              })
                            ->orWhere('first_name','like','%'.$searchValue.'%')
                            ->orWhere('last_name','like','%'.$searchValue.'%')
                            ->orWhere('email','like','%'.$searchValue.'%')
                            ->orWhere('phone_number','like','%'.$searchValue.'%');
                     });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();

        $data = array();

        foreach ($empQuery as $emp) {
          // echo "<pre>";print_r($emp->restaurant_branch);exit;
        ## Set dynamic route for action buttons
            // $emp['business_branch_name'] = ($emp->restaurant_branch) ? '<a href="'.route('business_branches.show',$emp->restaurant_branch->id).'">'.$emp->restaurant_branch->branch_name.'</a>' : '';          
            $emp['edit'] = route("staff.edit",$emp["id"]);
            $emp['show'] = route("staff.show",$emp["id"]);
            $emp['delete'] = route("staff.destroy",$emp["id"]);
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
      $page_title = trans('staff.add_new');
      $business_branches = BusinessBranch::where(['status' => 'active', 'business_id' => Auth::user()->business->id ])->get();
      return view('admin.staff.create',compact('page_title', 'business_branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      // echo '<pre>'; print_r($request->all()); die;
        $validator= $request->validate([
            'business_branch_id' => 'required',
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'numeric','min:10', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $request->request->add([
          'user_type'=> 'WaiterManager',
          'password' => Hash::make($request->password),
          'verified' => 1,
        ]);
        $data = $request->all();
        $data['vendor_id'] = Auth::user()->id;

        if(!isset($data['manage_reservations'])) {
          $data['manage_reservations'] = 0;
        }
        if(!isset($data['manage_waiting_list'])) {
          $data['manage_waiting_list'] = 0;
        }
        if(!isset($data['manage_pickups'])) {
          $data['manage_pickups'] = 0;
        }
        if(!isset($data['manage_catering_bookings'])) {
          $data['manage_catering_bookings'] = 0;
        }

        if($data['manage_reservations'] == 0 && $data['manage_waiting_list'] == 0 && $data['manage_pickups'] == 0 && $data['manage_catering_bookings'] == 0) {
          return redirect()->back()->withInput()->withError(trans('staff.choose_service'));
        }

        // echo '<pre>'; print_r($data); die;

        DB::beginTransaction();
        $staff = User::create($data);
        $role = Role::where('name','WaiterManager')->first();
        $staff->assignRole([$role->id]);
        if($staff) {
          $staff_data = [
            'business_branch_id' => $data['business_branch_id'],
            'staff_id' => $staff->id,
            'manage_reservations' => $data['manage_reservations'],
            'manage_waiting_list' => $data['manage_waiting_list'],
            'manage_pickups' => $data['manage_pickups'],
            'manage_catering_bookings' => $data['manage_catering_bookings'],
          ];
          $business_branch = BusinessBranchStaff::create($staff_data);
          DB::commit();
          return redirect()->route('staff.index')->with('success',trans('staff.added'));
        }else{
          DB::rollback();
          return redirect()->route('staff.index')->with('error',trans('staff.error'));
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
        $page_title = trans('staff.show');
        $business_branches = BusinessBranch::where(['status' => 'active', 'business_id' => Auth::user()->business->id ])->get();
        $staff = User::find($id);
        $selected_business_branch_staff = BusinessBranchStaff::where('staff_id', $staff->id)->first();
        $selected_business_branch = BusinessBranch::where('id', $selected_business_branch_staff->business_branch_id)->first();
        return view('admin.staff.show',compact('staff', 'business_branches','page_title', 'selected_business_branch_staff',  'selected_business_branch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('staff.update');
        $business_branches = BusinessBranch::where(['status' => 'active', 'business_id' => Auth::user()->business->id ])->get();
        $staff = User::find($id);
        if(!$staff) {
          return redirect()->route('staff.index')->withError(trans('common.no_data'));
        }
        $selected_business_branch_staff = BusinessBranchStaff::where('staff_id', $staff->id)->first();
        $selected_business_branch = BusinessBranch::where('id', $selected_business_branch_staff->business_branch_id)->first();
        // echo '<pre>'; print_r($manager->restaurant->id); die;
        return view('admin.staff.edit',compact('staff', 'business_branches','page_title', 'selected_business_branch_staff',  'selected_business_branch'));
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
        // echo '<pre>'; print_r($request->all()); die;
        $validator= $request->validate([
            'business_branch_id' => 'required',
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,id'],
            'phone_number' => ['required', 'numeric','min:10', 'unique:users,id,'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = $request->all();

        $staffusr = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'user_type'=> 'WaiterManager',
            'verified' => 1,
        ];
      

        if($request->has('password') && $request->password != '') { 
          // $request->request->add([
          //   'password' => Hash::make($request->password),
          // ]);
          $staffusr['password'] = Hash::make($request->password);
        }

        if(!isset($data['manage_reservations'])) {
          $data['manage_reservations'] = 0;
        }
        if(!isset($data['manage_waiting_list'])) {
          $data['manage_waiting_list'] = 0;
        }
        if(!isset($data['manage_pickups'])) {
          $data['manage_pickups'] = 0;
        }
        if(!isset($data['manage_catering_bookings'])) {
          $data['manage_catering_bookings'] = 0;
        }

        if($data['manage_reservations'] == 0 && $data['manage_waiting_list'] == 0 && $data['manage_pickups'] == 0 && $data['manage_catering_bookings'] == 0) {
          return redirect()->back()->withInput()->withError(trans('staff.choose_service'));
        }

        // $data = $request->all();

        $staff = User::find($id);
        // echo '<pre>'; print_r($data); die;

        DB::beginTransaction();
        if($staff) {
          $staff->update($staffusr);
          $staff_data = [
            'business_branch_id' => $data['business_branch_id'],
            'staff_id' => $staff->id,
            'manage_reservations' => $data['manage_reservations'],
            'manage_waiting_list' => $data['manage_waiting_list'],
            'manage_pickups' => $data['manage_pickups'],
            'manage_catering_bookings' => $data['manage_catering_bookings'],
          ];
          $business_branch = BusinessBranchStaff::where('staff_id',$staff->id)->update($staff_data);
          DB::commit();
          return redirect()->route('staff.index')->with('success',trans('staff.added'));
        }else{
          DB::rollback();
          return redirect()->route('staff.index')->with('error',trans('staff.error'));
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
        $cuisine = User::find($id);
        if($cuisine->delete()){
            return redirect()->route('staff.index')->with('success',trans('staff.deleted'));
        }else{
            return redirect()->route('staff.index')->with('error',trans('staff.error'));
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
        $cuisine= User::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($cuisine) {
        return response()->json(['success' => trans('staff.status_updated')]);
       } else {
        return response()->json(['error' => trans('staff.error')]);
       }
    }
}
   