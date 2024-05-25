<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodTruck;
use App\Models\FoodTruckMedia;
use App\Models\FoodTruckCuisine;
use App\Models\FoodTruckWorkingHour;
use App\Models\Cuisine;
use App\Models\Helpers\CommonHelpers;
use App\Models\Helpers\FoodTruckHelpers;


class FoodTruckController extends Controller
{
    use CommonHelpers, FoodTruckHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:food-truck-list', ['only' => ['index','show']]);
        $this->middleware('permission:food-truck-create', ['only' => ['create','store']]);
        $this->middleware('permission:food-truck-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('food_trucks.heading');
        $food_trucks = FoodTruck::all();
        $cuisines = Cuisine::where('status','active')->get();
        return view('admin.food_trucks.index',compact('food_trucks','page_title', 'cuisines'));
    }

    public function index_ajax(Request $request){
        $query = FoodTruck::query();
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

        $cuisines = @$request['cuisines'];
        ## Total number of record with filtering
        $filter= $query;
        if($searchValue != ''){
            $filter = $filter->whereHas('translation',function($query) use ($searchValue){
                        $query
                            ->where('name','like','%'.$searchValue.'%');
                          })
                        ->where(function($q)use ($searchValue) {
                            $q->where('link','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%')
                              ->orWhere('created_at','like','%'.$searchValue.'%');
                          });
        }


        //cuisines Filter
        if($cuisines && count($cuisines)){
          $food_truck_ids = FoodTruckCuisine::whereIn('cuisine_id', $cuisines)->pluck('food_truck_id')->toArray();
          $filter = $filter->whereIn('id', $food_truck_ids);
        }

        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'name' || $columnName == 'description'){
            $filter = $filter->join('food_truck_translations', 'food_truck_translations.food_truck_id', '=', 'food_trucks.id')
            ->orderBy('food_truck_translations.'.$columnName, $columnSortOrder)
            ->where('food_truck_translations.locale',\App::getLocale())
            ->select(['food_trucks.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['banner'] = '<img src="'.asset(FoodTruckMedia::where(['food_truck_id'=> $emp["id"], 'media_type' => 'banner'])->first()->media_path).'" style="width:50px">';
            $emp['cuisines'] = $this->cuisines($emp["id"], 'string');
            $emp['registration_date'] = date('d M Y', strtotime($emp["created_at"]));
            $emp['edit'] = route("food_trucks.edit",$emp["id"]);
            $emp['show'] = route("food_trucks.show",$emp["id"]);
            $emp['delete'] = route("food_trucks.destroy",$emp["id"]);
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
        $page_title = trans('food_trucks.add_new');
        $cuisines = Cuisine::where('status','active')->get();
        return view('admin.food_trucks.create',compact('page_title','cuisines'));
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
            'description:ar' => 'required',
            'description:en' => 'required',
            'link' => 'required|url|max:100',
            'cuisines' => 'required',
            'menus' => 'required',
            'menus.*' => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'banners' => 'required',
            'banners.*' => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'status' => 'required',
            'from_day' => 'required',
            'to_day' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);

        if(($request->from_day == $request->to_day) && $request->from_time == $request->to_time){
          return redirect()->back()->withInput($request->all())->with('error',trans('food_trucks.working_hours_wrong'));
        }
        $data = $request->all();
        // echo '<pre>'; print_r($data); die;

        $food_truck = FoodTruck::create($data);
        //create menus
          foreach($request->file('menus') as $menu) {
            $path = $this->saveMedia($menu,'menu');
            $food_truck_menus = FoodTruckMedia::create([
                  'food_truck_id' => $food_truck->id, 
                  'media_type' => 'menu', 
                  'media_path' => $path
                ]);
          }

        //create banners
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $food_truck_banners = FoodTruckMedia::create([
                  'food_truck_id' => $food_truck->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          }  

        //cuisines
          foreach ($request->cuisines as $cuisine) {
              FoodTruckCuisine::create([
                  'food_truck_id' => $food_truck->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          }  

        //Working Hours
          FoodTruckWorkingHour::where('food_truck_id', $food_truck->id)->delete();
          $working_hours = FoodTruckWorkingHour::create([
            'food_truck_id' => $food_truck->id,
            'from_day' => $request->from_day,
            'to_day' => $request->to_day,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
          ]);  

        if($food_truck && $food_truck_menus && $food_truck_banners && $working_hours) {
            return redirect()->route('food_trucks.index')->with('success',trans('food_trucks.added'));
        } else {
            return redirect()->route('food_trucks.index')->with('error',trans('food_trucks.error'));
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
        $page_title = trans('food_trucks.show');
        $food_truck = FoodTruck::find($id);
        if(!$food_truck) {
          return redirect()->route('food_trucks.index')->with('error',trans('common.no_data'));
        }
        $cuisines = Cuisine::where('status','active')->get();
        $selected_cuisines = FoodTruckCuisine::where('food_truck_id', $id)->pluck('cuisine_id')->toArray();
        $food_truck_working_hours = FoodTruckWorkingHour::where('food_truck_id',$id)->first();
        $food_truck_menus = FoodTruckMedia::where(['food_truck_id' => $id, 'media_type' => 'menu'])->select(['id','media_path'])->get()->toArray();
        $food_truck_banners = FoodTruckMedia::where(['food_truck_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        // echo '<pre>'; print_r($food_truck_menus); die;
        return view('admin.food_trucks.show',compact('food_truck','page_title','cuisines', 'selected_cuisines', 'food_truck_menus', 'food_truck_banners', 'food_truck_working_hours'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('food_trucks.update');
        $food_truck = FoodTruck::find($id);
        if(!$food_truck) {
          return redirect()->route('food_trucks.index')->with('error',trans('common.no_data'));
        }
        $cuisines = Cuisine::where('status','active')->get();
        $selected_cuisines = FoodTruckCuisine::where('food_truck_id', $id)->pluck('cuisine_id')->toArray();
        $food_truck_menus = FoodTruckMedia::where(['food_truck_id' => $id, 'media_type' => 'menu'])->select(['id','media_path'])->get()->toArray();
        $food_truck_banners = FoodTruckMedia::where(['food_truck_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        $food_truck_working_hours = FoodTruckWorkingHour::where('food_truck_id',$id)->first();
        // echo '<pre>'; print_r($food_truck_menus); die;
        return view('admin.food_trucks.edit',compact('food_truck','page_title','cuisines', 'selected_cuisines', 'food_truck_menus', 'food_truck_banners', 'food_truck_working_hours'));
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
      // print_r($request->all());die;
        $validator= $request->validate([
            'name:ar' => 'required|max:100',
            'name:en' => 'required|max:100',
            'description:ar' => 'required',
            'description:en' => 'required',
            'link' => 'required|url|max:100',
            'cuisines' => 'required',
            'menus.*' => 'image|mimes:png,jpg,jpeg|max:10000',
            'banners.*' => 'image|mimes:png,jpg,jpeg|max:10000',
            'status' => 'required',
            'from_day' => 'required',
            'to_day' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);

        $data = $request->all();
        $food_truck = FoodTruck::find($id);
        //create menus
        if($request->has('menus')){
          foreach($request->file('menus') as $menu) {
            $path = $this->saveMedia($menu,'menu');
            $food_truck_menus = FoodTruckMedia::create([
                  'food_truck_id' => $food_truck->id, 
                  'media_type' => 'menu', 
                  'media_path' => $path
                ]);
          }
        }

        //create banners
        if($request->has('banners')){
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $food_truck_banners = FoodTruckMedia::create([
                  'food_truck_id' => $food_truck->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          } 
        } 

        //cuisines
          FoodTruckCuisine::where(['food_truck_id' => $food_truck->id])->delete();
          foreach ($request->cuisines as $cuisine) {
              FoodTruckCuisine::create([
                  'food_truck_id' => $food_truck->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          } 

        //working hours
        FoodTruckWorkingHour::where('food_truck_id', $food_truck->id)->update([
          'from_day' => $request->from_day,
          'to_day' => $request->to_day,
          'from_time' => $request->from_time,
          'to_time' => $request->to_time,
        ]);

        if($food_truck->update($data)) {
            return redirect()->route('food_trucks.index')->with('success',trans('food_trucks.updated'));
        } else {
            return redirect()->route('food_trucks.index')->with('error',trans('food_trucks.error'));
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
            return redirect()->route('food_trucks.index')->with('success',trans('food_trucks.deleted'));
        }else{
            return redirect()->route('food_trucks.index')->with('error',trans('food_trucks.error'));
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
        return response()->json(['success' => trans('food_trucks.status_updated')]);
       } else {
        return response()->json(['error' => trans('food_trucks.error')]);
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
        $messge = trans('food_trucks.menu_deleted');
      }else{
        $messge = trans('food_trucks.banner_deleted');
      }       
      if($food_truck_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('food_trucks.error')]);
      }
    }
}
   