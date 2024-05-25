<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Cuisine;
use App\Models\BusinessMedia;
use App\Models\BusinessCuisine;
use App\Models\BusinessWorkingHour;
use App\Models\Cms;
use App\Models\Helpers\CommonHelpers;
use Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;


class BusinessController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:business-show', ['only' => ['show']]);
        $this->middleware('permission:business-list', ['only' => ['index']]);
        $this->middleware('permission:business-create', ['only' => ['create','store']]);
        $this->middleware('permission:business-status', ['only' => ['status']]);
        $this->middleware('permission:business-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('businesses.heading');
        $businesses = Business::all();
        $cuisines = Cuisine::where('status','active')->get();
        return view('admin.restaurants.index',compact('restaurants','page_title', 'cuisines'));
    }

    public function index_ajax(Request $request){
        $query = Business::query();
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
          $rest_ids = BusinessCuisine::whereIn('cuisine_id', $cuisines)->pluck('restaurant_id')->toArray();
          $filter = $filter->whereIn('id', $rest_ids);
        }

        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'name' || $columnName == 'description'){
            $filter = $filter->join('restaurant_translations', 'restaurant_translations.restaurant_id', '=', 'restaurants.id')
            ->orderBy('restaurant_translations.'.$columnName, $columnSortOrder)
            ->where('restaurant_translations.locale',App::getLocale())
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
            $emp['banner'] = '<img src="'.asset(BusinessMedia::where(['restaurant_id'=> $emp["id"], 'media_type' => 'banner'])->first()->media_path).'" style="width:50px">';
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
    public function create() {
        $page_title = trans('businesses.add_new');
        $cuisines = Cuisine::where('status','active')->get();
        $business = Business::where('vendor_id', Auth::user()->id)->first();
        if($business){
          return redirect()->route('businesses.edit',$business->id);
        }
        return view('admin.businesses.create',compact('page_title','cuisines'));
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
            'brand_name:ar' => 'required|max:100',
            'brand_name:en' => 'required|max:100',
            'description:ar' => 'required',
            'description:en' => 'required',
            'brand_email' => 'required|email|max:100',
            'brand_phone_number' => 'required|max:100',
            'link' => 'required|url|max:100',
            'cuisines' => 'required',
            // 'brand_logo' => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'banners' => 'required',
            'banners.*' => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'working_status' => 'required',
            // 'from_day' => 'required',
            // 'to_day' => 'required',
            'from_time' => 'required|date_format:h:i A',
            'to_time' => 'required|date_format:h:i A|after:from_time',
        ]);

        // if(($request->from_day == $request->to_day) && $request->from_time == $request->to_time){
        //   return redirect()->back()->withInput($request->all())->with('error',trans('businesses.working_hours_wrong'));
        // }



        DB::beginTransaction();
        $data = $request->all();

        $data['from_time'] = date("H:i",strtotime($data['from_time']));
        $data['to_time'] = date("H:i",strtotime($data['to_time']));


        $data['vendor_id'] = Auth::user()->id;

        if(!isset($data['sunday_serving']) && !isset($data['monday_serving']) && !isset($data['tuesday_serving']) && !isset($data['wednesday_serving']) && !isset($data['thursday_serving']) && !isset($data['friday_serving']) && !isset($data['saturday_serving'])){
          return redirect()->back()->withInput()->with('error',trans('catering_plans.choose_one_serving_day'));
        }

        if(!isset($data['sunday_serving'])){
          $data['sunday_serving'] = 0;
        } 
        if(!isset($data['monday_serving'])){
          $data['monday_serving'] = 0;
        } 
        if(!isset($data['tuesday_serving'])){
          $data['tuesday_serving'] = 0;
        }        
        if(!isset($data['wednesday_serving'])){
          $data['wednesday_serving'] = 0;
        }
        if(!isset($data['thursday_serving'])){
          $data['thursday_serving'] = 0;
        }
        if(!isset($data['friday_serving'])){
          $data['friday_serving'] = 0;
        }
        if(!isset($data['saturday_serving'])){
          $data['saturday_serving'] = 0;
        }

        //create Logo
        if(isset($data['brand_logo'])) {
         $path = $this->saveMedia($request->brand_logo,'brand_logo');
         $data['brand_logo'] = $path; 
        }
        // echo '<pre>'; print_r($data); die;

        $business = Business::create($data);


        //create banners
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $business_banners = BusinessMedia::create([
                  'business_id' => $business->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          }  

        //cuisines
          foreach ($request->cuisines as $cuisine) {
              BusinessCuisine::create([
                  'business_id' => $business->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          }

        //Working Hours
          BusinessWorkingHour::where('business_id', $business->id)->delete();
          $working_hours = BusinessWorkingHour::create([
            'business_id' => $business->id,
            'sunday_serving' => $data['sunday_serving'],
          'monday_serving' => $data['monday_serving'],
          'tuesday_serving' => $data['tuesday_serving'],
          'wednesday_serving' => $data['wednesday_serving'],
          'thursday_serving' => $data['thursday_serving'],
          'friday_serving' => $data['friday_serving'],
          'saturday_serving' => $data['saturday_serving'],
            'from_day' => 0,
            'to_day' => 0,
            'from_time' => $data['from_time'],
            'to_time' => $data['to_time'],
          ]);  

          $terms_conditions_data = [
            'page_name:en' => 'Terms and Conditions',
            'content:en' => '<p></p>',
            'page_name:ar' => 'Terms and Conditions',
            'content:ar' => '<p></p>',
            'slug' => 'terms_and_conditions',
            'display_order' => 1,
            'status'  => 1,
            'business_id' => $business->id,
          ];
          Cms::create($terms_conditions_data);

        if($business  && $business_banners && $working_hours) {
            DB::commit();
            return redirect()->route('businesses.create')->with('success',trans('businesses.added'));
        } else {
            DB::rollback();
            return redirect()->route('businesses.create')->with('error',trans('businesses.error'));
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
        $page_title = trans('businesses.show');
        $restaurant = Business::find($id);
        if(!$restaurant) {
          return redirect()->route('restaurants.index')->with('error',trans('common.no_data'));
        }
        $cuisines = Cuisine::where('status','active')->get();
        $selected_cuisines = BusinessCuisine::where('restaurant_id', $id)->pluck('cuisine_id')->toArray();
        $restaurant_working_hours = BusinessWorkingHour::where('restaurant_id',$id)->first();
        $restaurant_menus = BusinessMedia::where(['restaurant_id' => $id, 'media_type' => 'menu'])->select(['id','media_path'])->get()->toArray();
        $restaurant_banners = BusinessMedia::where(['restaurant_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
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
        $page_title = trans('businesses.update');
        $business = Business::find($id);
        if(!$business) {
          return redirect()->route('businesses.index')->with('error',trans('common.no_data'));
        }
        $cuisines = Cuisine::where('status','active')->get();
        $selected_cuisines = BusinessCuisine::where('business_id', $id)->pluck('cuisine_id')->toArray();
        $business_banners = BusinessMedia::where(['business_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        $business_working_hours = BusinessWorkingHour::where('business_id',$id)->first();
        return view('admin.businesses.edit',compact('business','page_title','cuisines', 'selected_cuisines', 'business_banners', 'business_working_hours'));
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
            'brand_name:ar' => 'required|max:100',
            'brand_name:en' => 'required|max:100',
            'description:ar' => 'required',
            'description:en' => 'required',
            'brand_email' => 'required|max:100',
            'brand_phone_number' => 'required|max:100',
            'link' => 'required|url|max:100',
            'cuisines' => 'required',
            // 'brand_logo' => 'image|mimes:png,jpg,jpeg|max:10000',
            'banners.*' => 'image|mimes:png,jpg,jpeg|max:10000',
            'working_status' => 'required',
            // 'from_day' => 'required',
            // 'to_day' => 'required',
            'from_time' => 'required|date_format:h:i A',
            'to_time' => 'required|date_format:h:i A|after:from_time',
        ]);

        $data = $request->all();
        $data['from_time'] = date("H:i",strtotime($data['from_time']));
        $data['to_time'] = date("H:i",strtotime($data['to_time']));

        $business = Business::find($id);

        if(!isset($data['sunday_serving']) && !isset($data['monday_serving']) && !isset($data['tuesday_serving']) && !isset($data['wednesday_serving']) && !isset($data['thursday_serving']) && !isset($data['friday_serving']) && !isset($data['saturday_serving'])){
          return redirect()->back()->withInput()->with('error',trans('catering_plans.choose_one_serving_day'));
        }


        DB::beginTransaction();
        //create menus
        if($request->has('brand_logo')) {
            unlink($business->brand_logo);
            $path = $this->saveMedia($request->brand_logo,'brand_logo');
            $data['brand_logo'] = $path; 
        }

        //create banners
        if($request->has('banners')){
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $business_banners = BusinessMedia::create([
                  'business_id' => $business->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          } 
        } 

        //cuisines
          BusinessCuisine::where(['business_id' => $business->id])->delete();
          foreach ($request->cuisines as $cuisine) {
              BusinessCuisine::create([
                  'business_id' => $business->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          } 

        //working hours
          if(!isset($data['sunday_serving'])){
          $data['sunday_serving'] = 0;
        } 
        if(!isset($data['monday_serving'])){
          $data['monday_serving'] = 0;
        } 
        if(!isset($data['tuesday_serving'])){
          $data['tuesday_serving'] = 0;
        }        
        if(!isset($data['wednesday_serving'])){
          $data['wednesday_serving'] = 0;
        }
        if(!isset($data['thursday_serving'])){
          $data['thursday_serving'] = 0;
        }
        if(!isset($data['friday_serving'])){
          $data['friday_serving'] = 0;
        }
        if(!isset($data['saturday_serving'])){
          $data['saturday_serving'] = 0;
        }

        BusinessWorkingHour::where('business_id', $business->id)->update([
          'sunday_serving' => $data['sunday_serving'],
          'monday_serving' => $data['monday_serving'],
          'tuesday_serving' => $data['tuesday_serving'],
          'wednesday_serving' => $data['wednesday_serving'],
          'thursday_serving' => $data['thursday_serving'],
          'friday_serving' => $data['friday_serving'],
          'saturday_serving' => $data['saturday_serving'],
          // 'from_day' => $request->from_day,
          // 'to_day' => $request->to_day,
          'from_time' => $data['from_time'],
          'to_time' => $data['to_time'],
        ]);

        if($business->update($data)) {
            DB::commit();
            return redirect()->back()->with('success',trans('businesses.updated'));
        } else {
            DB::rollback();
            return redirect()->back()->with('error',trans('businesses.error'));
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
        $restaurant = Business::find($id);
        if($restaurant->delete()){
            return redirect()->route('restaurants.index')->with('success',trans('businesses.deleted'));
        }else{
            return redirect()->route('restaurants.index')->with('error',trans('businesses.error'));
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
        $restaurant= Business::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($restaurant) {
        return response()->json(['success' => trans('businesses.status_updated')]);
       } else {
        return response()->json(['error' => trans('businesses.error')]);
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
        $restaurant= Business::where('id',$request->id)
               ->update(['working_status'=>$request->working_status]);
       if($restaurant) {
        return response()->json(['success' => trans('businesses.status_updated')]);
       } else {
        return response()->json(['error' => trans('businesses.error')]);
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
      $restaurant_media= BusinessMedia::where('id',$request->media_id)->first();
      if(file_exists($restaurant_media->media_path)){
        unlink($restaurant_media->media_path);
      }
      if($restaurant_media->media_type == 'menu'){
        $messge = trans('businesses.menu_deleted');
      }else{
        $messge = trans('businesses.banner_deleted');
      }       
      if($restaurant_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('businesses.error')]);
      }
    }
}
   