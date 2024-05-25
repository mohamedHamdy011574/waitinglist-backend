<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Country;
use App\Models\BusinessBranch;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:state-list', ['only' => ['index','show']]);
        $this->middleware('permission:state-create', ['only' => ['create','store']]);
        $this->middleware('permission:state-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:state-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $page_title = trans('states.heading');
        $states = State::all();
        return view('admin.states.index',compact('states','page_title'));
    }

     public function index_ajax(Request $request)
    {
        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        // $query = new City();  
        $query = State::query();
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter= $query;

        if($searchValue != ''){
        $filter= $filter->whereHas('country',function($query) use ($searchValue){
                          $query->where('country_name','like','%'.$searchValue.'%');
                         })->orWhere(function($q)use ($searchValue) {
                            $q->where('name','like','%'.$searchValue.'%');
                          //  ->orWhere('code','like','%'.$searchValue.'%')
                          //  ->orWhere('id','like','%'.$searchValue.'%')
                          //  ->orWhere('status','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        ## Fetch records
        $empQuery = $filter;
        $empQuery = $empQuery->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();
        foreach ($empQuery as $emp) {

        ## Foreign Key Value
           
            $emp['country_name']= $emp->country->country_name;


        ## Set dynamic route for action buttons
            $emp['edit']= route("states.edit",$emp["id"]);
            $emp['show']= route("states.show",$emp["id"]);
            $emp['delete'] = route("states.destroy",$emp["id"]);

            
          $data[]=$emp;
        }

        ## Response
        $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecordwithFilter,
          "iTotalDisplayRecords" => $totalRecords,
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
        $page_title = trans('states.add_new');
        $countries = Country::where('status','active')->get();
        return view('admin.states.create', compact('countries','page_title'));
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
             'name' => 'required|regex:/^[\w\-\s]+$/u|max:30|unique:states,name',
             'country_id' => 'required|exists:countries,id'
        ]);
       
        if(State::create($request->all())) {
            return redirect()->route('states.index')->with('success',trans('states.added'));
        } else {
            return redirect()->route('states.index')->with('error',trans('states.error'));
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
        $page_title = trans('states.show');
        $countries = Country::all();
        $state = State::find($id);
        return view('admin.states.show',compact('state','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $page_title = trans('states.update');
        $countries = Country::where('status','active')->get();
        $state = State::find($id);
        return view('admin.states.edit',compact('state','countries','page_title'));
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
        $data = $request->all();
        $state = State::find($id);

        if(empty($state)){
            return redirect()->route('states.index')->with('error',trans('states.error'));
        }

        $validator= $request->validate([
             'name'  =>['required','regex:/^[\w\-\s]+$/u','max:30',Rule::unique('states')->ignore($state->id)],
             'country_id' => 'required|exists:countries,id'
        ]);

        if($state->update($data)){
            return redirect()->route('states.index')->with('success',trans('states.updated'));
        } else {
            return redirect()->route('states.index')->with('error',trans('states.error'));
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
        $state = State::find($id);
        $states_associated = BusinessBranch::where('state_id', $id)->count();
        // echo $states_associated; die;
        if($states_associated > 0) {
            return redirect()->back()->with('error',trans('states.can_not_delete'));
        }

        if($state->delete()){
            return redirect()->route('states.index')->with('success',trans('states.deleted'));
        }else{
            return redirect()->route('states.index')->with('error',trans('states.error'));
        }
    }
    public function getStatesByCountry(Request $request)
    {
        $states = State::where('country_id',$request->country)->get();
        echo json_encode($states);
    }

    /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
    */
    public function get_state($country_id){
      try {
          $states = State::where('country_id',$country_id)->pluck('name','id');
          return response()->json(['success' => '1', 'data' => $states, 'message' => 'state_list']);
      } catch (Exception $e) {
          return response()->json(['success' => '0', 'data' => [], 'message' => $e->getMessage()]);
      }
    }
    
}