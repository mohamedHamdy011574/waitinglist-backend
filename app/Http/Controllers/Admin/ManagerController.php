<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manager-list', ['only' => ['index','show']]);
        $this->middleware('permission:manager-create', ['only' => ['create','store']]);
        $this->middleware('permission:manager-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('managers.heading');
        $managers = User::where('user_type','Manager')->get();
        return view('admin.managers.index',compact('managers','page_title'));
    }

    public function index_ajax(Request $request){
        $query = User::where('user_type', 'Manager');
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
                          $q->WhereHas('restaurant',function($que) use ($searchValue)  {
                                 $que->whereHas('translation',function($qu) use ($searchValue){
                                 $qu->where('name','like','%'.$searchValue.'%');
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
            $emp['restaurant_branch_name'] = ($emp->restaurant_branch) ? '<a href="'.route('restaurant_branches.show',$emp->restaurant_branch->id).'">'.$emp->restaurant_branch->name.'</a>' : '';          
            $emp['edit'] = route("managers.edit",$emp["id"]);
            $emp['show'] = route("managers.show",$emp["id"]);
            $emp['delete'] = route("managers.destroy",$emp["id"]);
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
      $page_title = trans('managers.add_new');
      $restaurant_branches = RestaurantBranch::where(['status' => 'active', ['manager_id','=',null]])->get();
      return view('admin.managers.create',compact('page_title', 'restaurant_branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator= $request->validate([
            'restaurant_branch_id' => 'required',
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'numeric','min:10', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $request->request->add([
          'user_type'=> 'Manager',
          'password' => Hash::make($request->password),
          'verified' => 1,
        ]);
        $data = $request->all();

        // echo '<pre>'; print_r($data); die;

        DB::beginTransaction();
        $manager = User::create($data);
        $manager->assignRole([5]);
        if($manager) {
          $restaurant_branch = RestaurantBranch::find($request->restaurant_branch_id);
          $restaurant_branch->manager_id = $manager->id;
          $restaurant_branch->save();
          DB::commit();
          return redirect()->route('managers.index')->with('success',trans('managers.added'));
        }else{
          DB::rollback();
          return redirect()->route('managers.index')->with('error',trans('managers.error'));
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
        $page_title = trans('managers.show');
        $manager = User::find($id);
        // echo '<pre>'; print_r($manager->restaurant->id); die;

        $m_restaurant_branch = RestaurantBranch::where(['status' => 'active', 'manager_id' => $id])->first();
        $restaurant_branches = RestaurantBranch::where(['status' => 'active', ['manager_id','=',null]])
          ->orWhere('manager_id', $id)->get();
        // print_r($manager->restaurant); die;
        // $restaurants
        return view('admin.managers.show',compact('manager','m_restaurant_branch', 'page_title','restaurant_branches'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('managers.update');
        $manager = User::find($id);
        // echo '<pre>'; print_r($manager->restaurant->id); die;

        $m_restaurant_branch = RestaurantBranch::where(['status' => 'active', 'manager_id' => $id])->first();
        $restaurant_branches = RestaurantBranch::where(['status' => 'active', ['manager_id','=',null]])
          ->orWhere('manager_id', $id)->get();
        // print_r($manager->restaurant); die;
        // $restaurants
        return view('admin.managers.edit',compact('m_restaurant_branch', 'manager', 'restaurant_branches','page_title'));
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
        $validator= $request->validate([
            'restaurant_branch_id' => 'required',
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,id'],
            'phone_number' => ['required', 'numeric','min:10', 'unique:users,id,'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'user_type'=> 'Manager',
            'verified' => 1,
        ];
      

        if($request->has('password') && $request->password != '') { 
          // $request->request->add([
          //   'password' => Hash::make($request->password),
          // ]);
          $data['password'] = Hash::make($request->password);
        }

        // $data = $request->all();

        $manager = User::find($id);

        // echo '<pre>'; print_r($data); die;

        DB::beginTransaction();
        if($manager) {
          $manager->update($data);

          RestaurantBranch::where('manager_id', $manager->id)->update(['manager_id' => null]);
          $restaurant_branch = RestaurantBranch::find($request->restaurant_branch_id);
          $restaurant_branch->manager_id = $manager->id;
          $restaurant_branch->save();
          DB::commit();
          return redirect()->route('managers.index')->with('success',trans('managers.added'));
        }else{
          DB::rollback();
          return redirect()->route('managers.index')->with('error',trans('managers.error'));
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
            return redirect()->route('managers.index')->with('success',trans('managers.deleted'));
        }else{
            return redirect()->route('managers.index')->with('error',trans('managers.error'));
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
        return response()->json(['success' => trans('managers.status_updated')]);
       } else {
        return response()->json(['error' => trans('managers.error')]);
       }
    }
}
   