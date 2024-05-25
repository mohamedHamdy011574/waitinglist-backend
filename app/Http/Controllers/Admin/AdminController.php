<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-list', ['only' => ['index','show']]);
        $this->middleware('permission:admin-create', ['only' => ['create','store']]);
        $this->middleware('permission:admin-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('admins.heading');
        $admins = User::where('user_type','Admin')->get();
        return view('admin.admins.index',compact('admins','page_title'));
    }

    public function index_ajax(Request $request){
        $query = User::where('user_type', 'Admin');
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
                          $q->orWhere('first_name','like','%'.$searchValue.'%')
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
        ## Set dynamic route for action buttons          
            $emp['edit'] = route("admins.edit",$emp["id"]);
            $emp['show'] = route("admins.show",$emp["id"]);
            $emp['delete'] = route("admins.destroy",$emp["id"]);
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

    public function create(){
      $page_title = trans('admins.add_new');
      return view('admin.admins.create',compact('page_title'));
    }
    public function store(Request $request)
    {
        $validator= $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'numeric','min:10', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $request->request->add([
          'user_type'=> 'Admin',
          'password' => Hash::make($request->password),
          'verified' => 1,
        ]);
        $data = $request->all();

        DB::beginTransaction();
        $admin = User::create($data);
        $admin->assignRole([3]);
        if($admin) {
          DB::commit();
          return redirect()->route('admins.index')->with('success',trans('admins.added'));
        }else{
          DB::rollback();
          return redirect()->route('admins.index')->with('error',trans('admins.error'));
        }
    }

    public function show($id)
    {
        $page_title = trans('admins.show');
        $admin = User::find($id);

        // $restaurants
        return view('admin.admins.show',compact('admin', 'page_title'));
    }

    public function edit($id)
    {
        $page_title = trans('admins.update');
        $admin = User::find($id);
        return view('admin.admins.edit',compact('admin','page_title'));
    }
    public function update(Request $request, $id)
    {
        $validator= $request->validate([
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
            'user_type'=> 'Admin',
            'verified' => 1,
        ];

      

        if($request->has('password') && $request->password != '') { 
          $data['password'] = Hash::make($request->password);
        }

        $admin = User::find($id);

        DB::beginTransaction();
        if($admin) {
          $admin->update($data);
          DB::commit();
          return redirect()->route('admins.index')->with('success',trans('admins.updated'));
        }else{
          DB::rollback();
          return redirect()->route('admins.index')->with('error',trans('admins.error'));
        }
    }

    public function destroy($id)
    {
        $admin = User::find($id);
        if($admin->delete()){
            return redirect()->route('admins.index')->with('success',trans('admins.deleted'));
        }else{
            return redirect()->route('admins.index')->with('error',trans('admins.error'));
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
        $admin= User::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($admin) {
        return response()->json(['success' => trans('admins.status_updated')]);
       } else {
        return response()->json(['error' => trans('admins.error')]);
       }
    }
}
   