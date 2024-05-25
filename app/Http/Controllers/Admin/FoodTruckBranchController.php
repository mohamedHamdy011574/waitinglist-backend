<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodTruck;
use App\Models\Country;
use App\Models\State;
use App\Models\FoodTruckBranch;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;

class FoodTruckBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:food-truck-branch-list', ['only' => ['index','show']]);
        $this->middleware('permission:food-truck-branch-create', ['only' => ['create','store']]);
        $this->middleware('permission:food-truck-branch-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('food_truck_branches.heading');
        $food_truck_branches = FoodTruckBranch::all();
        return view('admin.food_truck_branches.index',compact('food_truck_branches','page_title'));
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
        $query = FoodTruckBranch::query();
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter= $query;

        if($searchValue != ''){
        $filter = $filter->where(function($q)use ($searchValue) {
                          $q->whereHas('translation',function($query) use ($searchValue){
                                 $query->where('name','like','%'.$searchValue.'%')
                                        ->orWhere('address','like','%'.$searchValue.'%');
                                        })
                            ->orWhereHas('food_truck',function($que) use ($searchValue){
                                 $que->whereHas('translation',function($qu) use ($searchValue){
                                 $qu->where('name','like','%'.$searchValue.'%');
                                        });
                              })
                            // ->orWhere('status','like','%'.$searchValue.'%')
                            ->orWhere('created_at','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        if($columnName == 'name' || $columnName == 'address') {
            $filter = $filter->join('ftruck_branch_translations', 'ftruck_branch_translations.ftruck_branch_id', '=', 'ftruck_branches.id')
            ->orderBy('ftruck_branch_translations.'.$columnName, $columnSortOrder)
            ->where('ftruck_branch_translations.locale',\App::getLocale())
            ->select(['ftruck_branches.*']);
        }else if($columnName == 'food_truck_name') {
            $filter = $filter->join('food_trucks', 'food_trucks.id', '=', 'ftruck_branches.food_truck_id')
                      ->join('food_truck_translations', 'food_trucks.id', '=', 'food_truck_translations.food_truck_id')
            ->where('food_truck_translations.locale',\App::getLocale())
            ->orderBy('food_truck_translations.name', $columnSortOrder)
            ->select(['ftruck_branches.*']);
        }
        else {
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }

        ## Fetch records
        $empQuery = $filter;
        $empQuery = $empQuery->offset($row)->limit($rowperpage)->get();
        $data = array();
        foreach ($empQuery as $emp) {

        ## Foreign Key Value
           
            $emp['food_truck_name'] = '<a href="'.route('food_trucks.show',$emp->food_truck->id).'">'.$emp->food_truck->name.'</a>';


        ## Set dynamic route for action buttons
            $emp['edit'] = route("food_truck_branches.edit",$emp["id"]);
            $emp['show'] = route("food_truck_branches.show",$emp["id"]);
            $emp['delete'] = route("food_truck_branches.destroy",$emp["id"]);

            
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
        $page_title = trans('food_truck_branches.add_new');
        $food_trucks = FoodTruck::where('status','active')->get();
        $countries = Country::where('status','active')->get();
        return view('admin.food_truck_branches.create',compact('page_title','food_trucks','countries'));
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
            'name:ar' => 'required|max:100',
            'name:en' => 'required|max:100',
            'address:ar' => 'required',
            'address:en' => 'required',
            'food_truck_id' => 'required',
            'state_id' => 'required',
            'status' => 'required',
        ]);
        $data = $request->all();
        // echo '<pre>'; print_r($data); 
        DB::beginTransaction();
        $food_truck_branch = FoodTruckBranch::create($data);
        DB::commit();
        // echo '<pre>'; print_r($food_truck_branch); die; 
        
        if($food_truck_branch) {
          return redirect()->route('food_truck_branches.index')->with('success',trans('food_truck_branches.added'));
        } else {
          return redirect()->route('food_truck_branches.index')->with('error',trans('food_truck_branches.error'));
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
        $page_title = trans('food_truck_branches.show');
        $food_trucks = FoodTruck::where('status','active')->get();
        $states = State::all();
        $food_truck_branch = FoodTruckBranch::find($id);
        if(!$food_truck_branch) {
          return redirect()->route('food_truck_branches.index')->with('error',trans('common.no_data'));
        }
        $countries = Country::where('status','active')->get();
        $selected_country = State::with('country')->where('id',$food_truck_branch->state_id)->first();

        
        // echo '<pre>'; print_r($food_truck_menus); die;
        return view('admin.food_truck_branches.show',compact('food_trucks','states','countries','selected_country', 'food_truck_branch','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('food_truck_branches.update');
        $food_trucks = FoodTruck::where('status','active')->get();
        $countries = Country::where('status','active')->get();
        $states = State::all();
        $food_truck_branch = FoodTruckBranch::find($id);
        if(!$food_truck_branch) {
          return redirect()->route('food_truck_branches.index')->with('error',trans('common.no_data'));
        }
        $selected_country = State::with('country')->where('id',$food_truck_branch->state_id)->first();
        
        return view('admin.food_truck_branches.edit',compact('food_trucks','states','selected_country','countries','page_title','food_truck_branch'));
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
            'name:ar' => 'required|max:100',
            'name:en' => 'required|max:100',
            'address:ar' => 'required',
            'address:en' => 'required',
            'food_truck_id' => 'required',
            'state_id' => 'required',
            'status' => 'required',
        ]);

        $data = $request->all();
        $food_truck_branch = FoodTruckBranch::find($id);

        DB::beginTransaction();
        $food_truck_branch_update = $food_truck_branch->update($data);
        if($food_truck_branch) {
         
        }
        DB::commit();

        if($food_truck_branch_update) {
            return redirect()->route('food_truck_branches.index')->with('success',trans('food_truck_branches.updated'));
        } else {
            return redirect()->route('food_truck_branches.index')->with('error',trans('food_truck_branches.error'));
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
        $food_truck = FoodTruck::find($id);
        if($food_truck->delete()){
            return redirect()->route('food_truck_branches.index')->with('success',trans('food_truck_branches.deleted'));
        }else{
            return redirect()->route('food_truck_branches.index')->with('error',trans('food_truck_branches.error'));
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
        $food_truck= FoodTruck::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($food_truck) {
        return response()->json(['success' => trans('food_truck_branches.status_updated')]);
       } else {
        return response()->json(['error' => trans('food_truck_branches.error')]);
       }
    }

    /**
    * Ajax for remove food_truck media.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function remove_media(Request $request)
    {
      // print_r($request->all()); die;
      $food_truck_media= FoodTruckMedia::where('id',$request->media_id)->first();
      if(file_exists($food_truck_media->media_path)){
        unlink($food_truck_media->media_path);
      }
      if($food_truck_media->media_type == 'menu'){
        $messge = trans('food_truck_branches.menu_deleted');
      }else{
        $messge = trans('food_truck_branches.banner_deleted');
      }       
      if($food_truck_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('food_truck_branches.error')]);
      }
    }
}
   