<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantMedia;
use App\Models\RestaurantCuisine;
use App\Models\RestaurantWorkingHour;
use App\Models\Cuisine;
use App\Models\Helpers\CommonHelpers;
use App\Models\Helpers\RestaurantHelpers;


class RestaurantController extends Controller
{
    use CommonHelpers, RestaurantHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:restaurant-show', ['only' => ['show']]);
        $this->middleware('permission:restaurant-list', ['only' => ['index']]);
        $this->middleware('permission:restaurant-create', ['only' => ['create','store']]);
        $this->middleware('permission:restaurant-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('restaurants.heading');
        $restaurants = Restaurant::all();
        $cuisines = Cuisine::where('status','active')->get();
        return view('admin.restaurants.index',compact('restaurants','page_title', 'cuisines'));
    }

    public function index_ajax(Request $request){
        $query = Restaurant::query();
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
          $rest_ids = RestaurantCuisine::whereIn('cuisine_id', $cuisines)->pluck('restaurant_id')->toArray();
          $filter = $filter->whereIn('id', $rest_ids);
        }

        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'name' || $columnName == 'description'){
            $filter = $filter->join('restaurant_translations', 'restaurant_translations.restaurant_id', '=', 'restaurants.id')
            ->orderBy('restaurant_translations.'.$columnName, $columnSortOrder)
            ->where('restaurant_translations.locale',\App::getLocale())
            ->select(['restaurants.*']);
        } else if($columnName == 'registration_date'){
          $columnName = 'created_at';
        } else {
          $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['banner'] = '<img src="'.asset(RestaurantMedia::where(['restaurant_id'=> $emp["id"], 'media_type' => 'banner'])->first()->media_path).'" style="width:50px">';
            $emp['cuisines'] = $this->cuisines($emp["id"], 'string');
            $emp['registration_date'] = date('d M Y', strtotime($emp["created_at"]));
            $emp['edit'] = route("restaurants.edit",$emp["id"]);
            $emp['show'] = route("restaurants.show",$emp["id"]);
            $emp['delete'] = route("restaurants.destroy",$emp["id"]);
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
        $page_title = trans('restaurants.add_new');
        $cuisines = Cuisine::where('status','active')->get();
        return view('admin.restaurants.create',compact('page_title','cuisines'));
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
            'working_status' => 'required',
            'from_day' => 'required',
            'to_day' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);

        if(($request->from_day == $request->to_day) && $request->from_time == $request->to_time){
          return redirect()->back()->withInput($request->all())->with('error',trans('restaurants.working_hours_wrong'));
        }
        $data = $request->all();
        // echo '<pre>'; print_r($data); die;

        $restaurant = Restaurant::create($data);
        //create menus
          foreach($request->file('menus') as $menu) {
            $path = $this->saveMedia($menu,'menu');
            $restaurant_menus = RestaurantMedia::create([
                  'restaurant_id' => $restaurant->id, 
                  'media_type' => 'menu', 
                  'media_path' => $path
                ]);
          }

        //create banners
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $restaurant_banners = RestaurantMedia::create([
                  'restaurant_id' => $restaurant->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          }  

        //cuisines
          foreach ($request->cuisines as $cuisine) {
              RestaurantCuisine::create([
                  'restaurant_id' => $restaurant->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          }  

        //Working Hours
          RestaurantWorkingHour::where('restaurant_id', $restaurant->id)->delete();
          $working_hours = RestaurantWorkingHour::create([
            'restaurant_id' => $restaurant->id,
            'from_day' => $request->from_day,
            'to_day' => $request->to_day,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
          ]);  

        if($restaurant && $restaurant_menus && $restaurant_banners && $working_hours) {
            return redirect()->route('restaurants.index')->with('success',trans('restaurants.added'));
        } else {
            return redirect()->route('restaurants.index')->with('error',trans('restaurants.error'));
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
        $page_title = trans('restaurants.show');
        $restaurant = Restaurant::find($id);
        if(!$restaurant) {
          return redirect()->route('restaurants.index')->with('error',trans('common.no_data'));
        }
        $cuisines = Cuisine::where('status','active')->get();
        $selected_cuisines = RestaurantCuisine::where('restaurant_id', $id)->pluck('cuisine_id')->toArray();
        $restaurant_working_hours = RestaurantWorkingHour::where('restaurant_id',$id)->first();
        $restaurant_menus = RestaurantMedia::where(['restaurant_id' => $id, 'media_type' => 'menu'])->select(['id','media_path'])->get()->toArray();
        $restaurant_banners = RestaurantMedia::where(['restaurant_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        // echo '<pre>'; print_r($restaurant_menus); die;
        return view('admin.restaurants.show',compact('restaurant','page_title','cuisines', 'selected_cuisines', 'restaurant_menus', 'restaurant_banners', 'restaurant_working_hours'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('restaurants.update');
        $restaurant = Restaurant::find($id);
        if(!$restaurant) {
          return redirect()->route('restaurants.index')->with('error',trans('common.no_data'));
        }
        $cuisines = Cuisine::where('status','active')->get();
        $selected_cuisines = RestaurantCuisine::where('restaurant_id', $id)->pluck('cuisine_id')->toArray();
        $restaurant_menus = RestaurantMedia::where(['restaurant_id' => $id, 'media_type' => 'menu'])->select(['id','media_path'])->get()->toArray();
        $restaurant_banners = RestaurantMedia::where(['restaurant_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        $restaurant_working_hours = RestaurantWorkingHour::where('restaurant_id',$id)->first();
        // echo '<pre>'; print_r($restaurant_menus); die;
        return view('admin.restaurants.edit',compact('restaurant','page_title','cuisines', 'selected_cuisines', 'restaurant_menus', 'restaurant_banners', 'restaurant_working_hours'));
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
            'working_status' => 'required',
            'from_day' => 'required',
            'to_day' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);

        $data = $request->all();
        $restaurant = Restaurant::find($id);
        //create menus
        if($request->has('menus')){
          foreach($request->file('menus') as $menu) {
            $path = $this->saveMedia($menu,'menu');
            $restaurant_menus = RestaurantMedia::create([
                  'restaurant_id' => $restaurant->id, 
                  'media_type' => 'menu', 
                  'media_path' => $path
                ]);
          }
        }

        //create banners
        if($request->has('banners')){
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $restaurant_banners = RestaurantMedia::create([
                  'restaurant_id' => $restaurant->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          } 
        } 

        //cuisines
          RestaurantCuisine::where(['restaurant_id' => $restaurant->id])->delete();
          foreach ($request->cuisines as $cuisine) {
              RestaurantCuisine::create([
                  'restaurant_id' => $restaurant->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          } 

        //working hours
        RestaurantWorkingHour::where('restaurant_id', $restaurant->id)->update([
          'from_day' => $request->from_day,
          'to_day' => $request->to_day,
          'from_time' => $request->from_time,
          'to_time' => $request->to_time,
        ]);

        if($restaurant->update($data)) {
            return redirect()->route('restaurants.index')->with('success',trans('restaurants.updated'));
        } else {
            return redirect()->route('restaurants.index')->with('error',trans('restaurants.error'));
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
        $restaurant = Restaurant::find($id);
        if($restaurant->delete()){
            return redirect()->route('restaurants.index')->with('success',trans('restaurants.deleted'));
        }else{
            return redirect()->route('restaurants.index')->with('error',trans('restaurants.error'));
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
        $restaurant= Restaurant::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($restaurant) {
        return response()->json(['success' => trans('restaurants.status_updated')]);
       } else {
        return response()->json(['error' => trans('restaurants.error')]);
       }
    }

    /**
    * Ajax for index page working status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function workingStatus(Request $request)
    {
        $restaurant= Restaurant::where('id',$request->id)
               ->update(['working_status'=>$request->working_status]);
       if($restaurant) {
        return response()->json(['success' => trans('restaurants.status_updated')]);
       } else {
        return response()->json(['error' => trans('restaurants.error')]);
       }
    }

    /**
    * Ajax for remove restaurant media.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function remove_media(Request $request)
    {
      // print_r($request->all()); die;
      $restaurant_media= RestaurantMedia::where('id',$request->media_id)->first();
      if(file_exists($restaurant_media->media_path)){
        unlink($restaurant_media->media_path);
      }
      if($restaurant_media->media_type == 'menu'){
        $messge = trans('restaurants.menu_deleted');
      }else{
        $messge = trans('restaurants.banner_deleted');
      }       
      if($restaurant_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('restaurants.error')]);
      }
    }
}
   