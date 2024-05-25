<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = trans('title.city_list');
        $cities = City::all();
        return view('admin.cities.index',compact('cities','page_title'));
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
        $query = City::query();
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter= $query;

        if($searchValue != ''){
        $filter= $filter->whereHas('state',function($query) use ($searchValue){
                          $query->where('name','like','%'.$searchValue.'%');
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
           
            $emp['state_name']= $emp->state->name;
            $emp['country_name']= $emp->state->country->country_name;


        ## Set dynamic route for action buttons
            $emp['edit']= route("cities.edit",$emp["id"]);
            $emp['show']= route("cities.show",$emp["id"]);
            $emp['delete'] = route("cities.destroy",$emp["id"]);

            
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
        $page_title = trans('title.city_create');
        $states = State::all();
        return view('admin.cities.create', compact('states','page_title'));
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
            'name' => 'required|regex:/^[\w\-\s]+$/u|max:30|unique:cities,name',
            'state_id' => 'required|exists:states,id'
        ]);
       
        if(City::create($request->all())) {
            return redirect()->route('cities.index')->with('success',trans('cities.added'));
        } else {
            return redirect()->route('cities.index')->with('error','Something went wrong');
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
        $page_title = trans('title.city_detail');
        $countries = Country::all();
        $city = City::find($id);
        return view('admin.cities.show',compact('city','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('title.city_edit');
        $city = City::find($id);
        $states = State::all();
        // echo $city->state->country->id; die;
        //$state_id = $city->state_id;
        //$country_id = 
        //$countries = Country::where('state_id', )::all();
        return view('admin.cities.edit',compact('city','states','page_title'));
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
        $city = City::find($id);

        if(empty($city)){
            return redirect()->route('cities.index')->with('error',trans('cities.error'));
        }

        $validator= $request->validate([
            'name'  =>['required','regex:/^[\w\-\s]+$/u','max:30',Rule::unique('cities')->ignore($city->id)],
            'state_id' => 'required|exists:states,id'
        ]);

        if($city->update($data)){
            return redirect()->route('cities.index')->with('success',trans('cities.updated'));
        } else {
            return redirect()->route('cities.index')->with('error',trans('cities.error'));
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
        $city = City::find($id);

        if($city->delete()){
            return redirect()->route('cities.index')->with('success',trans('cities.deleted'));
        }else{
            return redirect()->route('cities.index')->with('error',trans('cities.error'));
        }
    }


    public function get_states_from_country(Request $request) { // AJAX
        $data = $request->all();
        //print_r($data);die;
        $states = State::where('country_id', $data['country_id'])->get()->all();
        echo "<option value=''>".trans('cities.select_state')."</option>";

        foreach ($states as $state) {
            echo "<option value=" . $state->id . ">" . $state->name . "</option>";
        }
        
    }
}