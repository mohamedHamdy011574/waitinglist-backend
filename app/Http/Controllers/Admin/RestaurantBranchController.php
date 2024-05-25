<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Country;
use App\Models\State;
use App\Models\RestaurantBranch;
use App\Models\SeatingArea;
use App\Models\RestaurantTiming;
use App\Models\RestaurantBranchSeating;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;

class RestaurantBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:restaurant-branch-list', ['only' => ['index','show']]);
        $this->middleware('permission:restaurant-branch-create', ['only' => ['create','store']]);
        $this->middleware('permission:restaurant-branch-edit', ['only' => ['edit','update']]);
    }

    public function index() {
        $page_title = trans('restaurant_branches.heading');
        $seating_areas = SeatingArea::all();
        return view('admin.restaurant_branches.index',compact('seating_areas','page_title'));
    }

    public function index_ajax(Request $request) { 
        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        // $query = new City();  
        $user = auth()->user();
        if($user->user_type == 'Manager') {
          // $query = RestaurantBranch::query();
          $query = RestaurantBranch::where('manager_id', $user->id);
        } else {
          $query = RestaurantBranch::query();
        }
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
                            ->orWhereHas('restaurant',function($que) use ($searchValue){
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
            $filter = $filter->join('rest_branch_translations', 'rest_branch_translations.rest_branch_id', '=', 'rest_branches.id')
            ->orderBy('rest_branch_translations.'.$columnName, $columnSortOrder)
            ->where('rest_branch_translations.locale',\App::getLocale())
            ->select(['rest_branches.*']);
        }else if($columnName == 'restaurant_name') {
            $filter = $filter->join('restaurants', 'restaurants.id', '=', 'rest_branches.restaurant_id')
                      ->join('restaurant_translations', 'restaurants.id', '=', 'restaurant_translations.restaurant_id')
            ->where('restaurant_translations.locale',\App::getLocale())
            ->orderBy('restaurant_translations.name', $columnSortOrder)
            ->select(['rest_branches.*']);
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
           
            $emp['restaurant_name'] = '<a href="'.route('restaurants.show',$emp->restaurant->id).'">'.$emp->restaurant->name.'</a>';


        ## Set dynamic route for action buttons
            $emp['edit'] = route("restaurant_branches.edit",$emp["id"]);
            $emp['show'] = route("restaurant_branches.show",$emp["id"]);
            $emp['delete'] = route("restaurant_branches.destroy",$emp["id"]);

            
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
    public function create() {
        $page_title = trans('restaurant_branches.add_new');
        $restaurants = Restaurant::where('status','active')->get();
        $countries = Country::where('status','active')->get();
        $seating_areas = SeatingArea::where('status','active')->get();
        return view('admin.restaurant_branches.create',compact('page_title','restaurants','seating_areas','countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator= $request->validate([
            'name:ar' => 'required|max:100',
            'name:en' => 'required|max:100',
            /*'address:ar' => 'required',
            'address:en' => 'required',*/
            'address_autocom' => 'required',
            'total_seats' => 'required',
            // 'available_seats' => 'required',
            'restaurant_id' => 'required',
            // 'state_id' => 'required',
            'stg_area_id' => 'required',
            'status' => 'required',
        ]);
        $data = $request->all();

        $state_id = State::where('name',$request->state)->first();
        $data['state_id'] = $state_id;
        // echo '<pre>'; print_r($data); die;
        DB::beginTransaction();
        $restaurant_branch = RestaurantBranch::create($data);

        
        if($restaurant_branch) {
          /*//SAVE TIMINGS
          RestaurantTiming::where('rest_branch_id', $restaurant_branch->id)->delete();
          $days = [ 0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5=> 'friday', 6 => 'saturday'];
          $count = 0;
          foreach ($days as $d => $dname) {
              $timing_data = [];
              if(isset($data[$dname.'_from']) && isset($data[$dname.'_to'])) {

                  //validation
                  if($data[$dname.'_from'] >= $data[$dname.'_to']){
                      DB::rollback();
                      return Redirect::back()->with('error',trans('restaurant_branches.invalid_time'))->withInput();
                  }
                  // if(!isset($data[$d.'_reservations_capacity'])){
                  //     DB::rollback();
                  //     return 'invalid_capacity';
                  // }

                  $timing_data = [
                      'rest_branch_id' => $restaurant_branch->id,
                      'week_day' => $d,
                      'from' => $data[$dname.'_from'],
                      'to' => $data[$dname.'_to'],
                      'reservations_capacity' => 0,
                  ];

                  // print_r($timing_data);
                  $timing = RestaurantTiming::create($timing_data);
                  $count++;
              }
          }*/

          //save restaurant seating
          foreach ($request->stg_area_id as $stg_area) {
              RestaurantBranchSeating::create([
                  'rest_branch_id' => $restaurant_branch->id, 
                  'stg_area_id' => $stg_area, 
              ]);
          }          
        }
        DB::commit();
        
        if($restaurant_branch) {
          return redirect()->route('restaurant_branches.index')->with('success',trans('restaurant_branches.added'));
        } else {
          return redirect()->route('restaurant_branches.index')->with('error',trans('restaurant_branches.error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('restaurant_branches.show');
        $restaurants = Restaurant::where('status','active')->get();
        $states = State::all();
        $restaurant_branch = RestaurantBranch::find($id);
        if(!$restaurant_branch) {
          return redirect()->route('restaurant_branches.index')->with('error',trans('common.no_data'));
        }
        $countries = Country::where('status','active')->get();
        $selected_country = State::with('country')->where('id',$restaurant_branch->state_id)->first();

        $seating_areas = SeatingArea::where('status','active')->get();
        $selected_seating_areas = RestaurantBranchSeating::where('rest_branch_id', $id)->pluck('stg_area_id')->toArray();
        $timings = [];
        if($restaurant_branch) {
          $timings = RestaurantTiming::where('rest_branch_id', $restaurant_branch->id)->get()->toArray();
        }

        $restaurant_branch_timings['sunday_from'] = 
        $restaurant_branch_timings['sunday_to'] = 
        $restaurant_branch_timings['monday_from'] = 
        $restaurant_branch_timings['monday_to'] = 
        $restaurant_branch_timings['tuesday_from'] = 
        $restaurant_branch_timings['tuesday_to'] = 
        $restaurant_branch_timings['wednesday_from'] = 
        $restaurant_branch_timings['wednesday_to'] = 
        $restaurant_branch_timings['thursday_from'] = 
        $restaurant_branch_timings['thursday_to'] = 
        $restaurant_branch_timings['friday_from'] = 
        $restaurant_branch_timings['friday_to'] = 
        $restaurant_branch_timings['saturday_from'] = 
        $restaurant_branch_timings['saturday_to'] = '';
        foreach ($timings as $time) {
          if($time['week_day'] == 0){
            $restaurant_branch_timings['sunday_from'] = $time['from'];  
            $restaurant_branch_timings['sunday_to'] = $time['to'];  
          }
          if($time['week_day'] == 1){
            $restaurant_branch_timings['monday_from'] = $time['from'];  
            $restaurant_branch_timings['monday_to'] = $time['to'];  
          }
          if($time['week_day'] == 2){
            $restaurant_branch_timings['tuesday_from'] = $time['from'];  
            $restaurant_branch_timings['tuesday_to'] = $time['to'];  
          }
          if($time['week_day'] == 3){
            $restaurant_branch_timings['wednesday_from'] = $time['from'];  
            $restaurant_branch_timings['wednesday_to'] = $time['to'];  
          }
          if($time['week_day'] == 4){
            $restaurant_branch_timings['thursday_from'] = $time['from'];  
            $restaurant_branch_timings['thursday_to'] = $time['to'];  
          }
          if($time['week_day'] == 5){
            $restaurant_branch_timings['friday_from'] = $time['from'];  
            $restaurant_branch_timings['friday_to'] = $time['to'];  
          }
          if($time['week_day'] == 6){
            $restaurant_branch_timings['saturday_from'] = $time['from'];  
            $restaurant_branch_timings['saturday_to'] = $time['to'];  
          }
        }
        // echo '<pre>'; print_r($restaurant_menus); die;
        return view('admin.restaurant_branches.show',compact('restaurants','states','countries','selected_country', 'restaurant_branch','page_title','restaurant_branch_timings','seating_areas','selected_seating_areas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page_title = trans('restaurant_branches.update');
        $restaurants = Restaurant::where('status','active')->get();
        $countries = Country::where('status','active')->get();
        $states = State::all();
        $seating_areas = SeatingArea::where('status','active')->get();
        $selected_seating_areas = RestaurantBranchSeating::where('rest_branch_id', $id)->pluck('stg_area_id')->toArray();
        $restaurant_branch = RestaurantBranch::find($id);
        if(!$restaurant_branch) {
          return redirect()->route('restaurant_branches.index')->with('error',trans('common.no_data'));
        }
        $selected_country = State::with('country')->where('id',$restaurant_branch->state_id)->first();
        $timings = [];
        if($restaurant_branch) {
          $timings = RestaurantTiming::where('rest_branch_id', $restaurant_branch->id)->get()->toArray();
        }

        $restaurant_branch_timings['sunday_from'] = 
        $restaurant_branch_timings['sunday_to'] = 
        $restaurant_branch_timings['monday_from'] = 
        $restaurant_branch_timings['monday_to'] = 
        $restaurant_branch_timings['tuesday_from'] = 
        $restaurant_branch_timings['tuesday_to'] = 
        $restaurant_branch_timings['wednesday_from'] = 
        $restaurant_branch_timings['wednesday_to'] = 
        $restaurant_branch_timings['thursday_from'] = 
        $restaurant_branch_timings['thursday_to'] = 
        $restaurant_branch_timings['friday_from'] = 
        $restaurant_branch_timings['friday_to'] = 
        $restaurant_branch_timings['saturday_from'] = 
        $restaurant_branch_timings['saturday_to'] = '';
        foreach ($timings as $time) {
          if($time['week_day'] == 0){
            $restaurant_branch_timings['sunday_from'] = $time['from'];  
            $restaurant_branch_timings['sunday_to'] = $time['to'];  
          }
          if($time['week_day'] == 1){
            $restaurant_branch_timings['monday_from'] = $time['from'];  
            $restaurant_branch_timings['monday_to'] = $time['to'];  
          }
          if($time['week_day'] == 2){
            $restaurant_branch_timings['tuesday_from'] = $time['from'];  
            $restaurant_branch_timings['tuesday_to'] = $time['to'];  
          }
          if($time['week_day'] == 3){
            $restaurant_branch_timings['wednesday_from'] = $time['from'];  
            $restaurant_branch_timings['wednesday_to'] = $time['to'];  
          }
          if($time['week_day'] == 4){
            $restaurant_branch_timings['thursday_from'] = $time['from'];  
            $restaurant_branch_timings['thursday_to'] = $time['to'];  
          }
          if($time['week_day'] == 5){
            $restaurant_branch_timings['friday_from'] = $time['from'];  
            $restaurant_branch_timings['friday_to'] = $time['to'];  
          }
          if($time['week_day'] == 6){
            $restaurant_branch_timings['saturday_from'] = $time['from'];  
            $restaurant_branch_timings['saturday_to'] = $time['to'];  
          }
        }
        return view('admin.restaurant_branches.edit',compact('restaurants','states','selected_country','countries','seating_areas','selected_seating_areas','page_title','restaurant_branch', 'restaurant_branch_timings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator= $request->validate([
            'name:ar' => 'required|max:100',
            'name:en' => 'required|max:100',
            'address:ar' => 'required',
            'address:en' => 'required',
            'total_seats' => 'required',
            // 'available_seats' => 'required',
            'restaurant_id' => 'required',
            'state_id' => 'required',
            'stg_area_id' => 'required',
            'status' => 'required',
        ]);

        $data = $request->all();
        $restaurant_branch = RestaurantBranch::find($id);

        DB::beginTransaction();
        $restaurant_branch_update = $restaurant_branch->update($data);
        if($restaurant_branch) {
          

          //restaurant branch seating
          RestaurantBranchSeating::where(['rest_branch_id' => $restaurant_branch->id])->delete();
          foreach ($request->stg_area_id as $stg_area) {
              RestaurantBranchSeating::create([
                  'rest_branch_id' => $restaurant_branch->id, 
                  'stg_area_id' => $stg_area, 
              ]);
          } 
        }
        DB::commit();

        if($restaurant_branch_update) {
            return redirect()->route('restaurant_branches.index')->with('success',trans('restaurant_branches.updated'));
        } else {
            return redirect()->route('restaurant_branches.index')->with('error',trans('restaurant_branches.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $restaurant = Restaurant::find($id);
        if($restaurant->delete()){
            return redirect()->route('restaurant_branches.index')->with('success',trans('restaurant_branches.deleted'));
        }else{
            return redirect()->route('restaurant_branches.index')->with('error',trans('restaurant_branches.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request) {
        $restaurant= Restaurant::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($restaurant) {
        return response()->json(['success' => trans('restaurant_branches.status_updated')]);
       } else {
        return response()->json(['error' => trans('restaurant_branches.error')]);
       }
    }

    /**
    * Ajax for remove restaurant media.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function remove_media(Request $request) {
      // print_r($request->all()); die;
      $restaurant_media= RestaurantMedia::where('id',$request->media_id)->first();
      if(file_exists($restaurant_media->media_path)){
        unlink($restaurant_media->media_path);
      }
      if($restaurant_media->media_type == 'menu'){
        $messge = trans('restaurant_branches.menu_deleted');
      }else{
        $messge = trans('restaurant_branches.banner_deleted');
      }       
      if($restaurant_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('restaurant_branches.error')]);
      }
    }
}
   