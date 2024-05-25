<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catering;
use App\Models\CateringMedia;
use App\Models\CateringWorkingHour;
use App\Models\CateringCuisine;
use App\Models\Cuisine;
use App\Models\Setting;
use App\Models\Country;
use App\Models\State;
use App\Models\Helpers\CommonHelpers;
use App\Models\Helpers\CateringHelpers;
use Illuminate\Support\Facades\App;

class CateringController extends Controller
{
    use CommonHelpers, CateringHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:catering-list', ['only' => ['index','show']]);
        $this->middleware('permission:catering-create', ['only' => ['create','store']]);
        $this->middleware('permission:catering-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('catering.heading');
        $catering = Catering::all();
        $cuisines = Cuisine::where('status','active')->get();
        $currency = Setting::get('currency');
        return view('admin.catering.index',compact('catering','page_title','currency','cuisines'));
    }

    public function index_ajax(Request $request){
        $query = Catering::query();
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
                            ->where('name','like','%'.$searchValue.'%')
                            ->orWhere('description','like','%'.$searchValue.'%');
                          })
                        ->where(function($q)use ($searchValue) {
                            $q->where('link','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%')
                            ->orWhere('price','like','%'.$searchValue.'%')
                              ->orWhere('created_at','like','%'.$searchValue.'%');
                          });
        }

        //cuisines Filter
        if($cuisines && count($cuisines)){
          $catr_ids = CateringCuisine::whereIn('cuisine_id', $cuisines)->pluck('catering_id')->toArray();
          $filter = $filter->whereIn('id', $catr_ids);
        }

        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'name' || $columnName == 'description'){
            $filter = $filter->join('catering_translations', 'catering_translations.catering_id', '=', 'catering.id')
            ->orderBy('catering_translations.'.$columnName, $columnSortOrder)
            ->where('catering_translations.locale',App::getLocale())
            ->select(['catering.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['banner'] = '<img src="'.asset(CateringMedia::where(['catering_id'=> $emp["id"], 'media_type' => 'banner'])->first()->media_path).'" style="width:50px">';
            $emp['registration_date'] = date('d M Y', strtotime($emp["created_at"]));
            $emp['cuisines'] = $this->cuisines($emp["id"], 'string');
            $emp['edit'] = route("catering.edit",$emp["id"]);
            $emp['show'] = route("catering.show",$emp["id"]);
            $emp['delete'] = route("catering.destroy",$emp["id"]);
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
        $page_title = trans('catering.add_new');
        $currency = Setting::get('currency');
        $countries = Country::where('status','active')->get();
        $cuisines = Cuisine::where('status','active')->get();
        return view('admin.catering.create',compact('page_title', 'currency', 'countries', 'cuisines'));
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
            'name:ar' => 'required|max:100',
            'name:en' => 'required|max:100',
            'description:ar' => 'required',
            'description:en' => 'required',
            'food_serving:ar' => 'required',
            'food_serving:en' => 'required',
            'address:ar' => 'required',
            'address:en' => 'required',
            'link' => 'required|url|max:100',
            'banners' => 'required',
            'banners.*' => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'status' => 'required',
            'from_day' => 'required',
            'to_day' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
            'price' => 'required|numeric',
            'cuisines' => 'required',
            'state_id' => 'required',
        ]);

        if(($request->from_day == $request->to_day) && $request->from_time == $request->to_time){
          return redirect()->back()->withInput($request->all())->with('error',trans('catering.working_hours_wrong'));
        }
        $data = $request->all();
        // echo '<pre>'; print_r($data); die;

        $catering = Catering::create($data);

        //create banners
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $catering_banners = CateringMedia::create([
                  'catering_id' => $catering->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          }

        //cuisines
          foreach ($request->cuisines as $cuisine) {
              CateringCuisine::create([
                  'catering_id' => $catering->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          } 

        //Working Hours
          CateringWorkingHour::where('catering_id', $catering->id)->delete();
          $working_hours = CateringWorkingHour::create([
            'catering_id' => $catering->id,
            'from_day' => $request->from_day,
            'to_day' => $request->to_day,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
          ]);  

        if($catering && $catering_banners && $working_hours) {
            return redirect()->route('catering.index')->with('success',trans('catering.added'));
        } else {
            return redirect()->route('catering.index')->with('error',trans('catering.error'));
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
        $page_title = trans('catering.show');
        $catering = Catering::find($id);
        if(!$catering) {
          return redirect()->route('catering.index')->with('error',trans('common.no_data'));
        }
        $states = State::all();
        $countries = Country::where('status','active')->get();
        $selected_country = State::with('country')->where('id',$catering->state_id)->first();

        $currency = Setting::get('currency');
        $cuisines = Cuisine::where('status','active')->get();
        $selected_cuisines = CateringCuisine::where('catering_id', $id)->pluck('cuisine_id')->toArray();
        $catering_working_hours = CateringWorkingHour::where('catering_id',$id)->first();
        $catering_menus = CateringMedia::where(['catering_id' => $id, 'media_type' => 'menu'])->select(['id','media_path'])->get()->toArray();
        $catering_banners = CateringMedia::where(['catering_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        // echo '<pre>'; print_r($catering_menus); die;
        return view('admin.catering.show',compact('catering','page_title', 'catering_menus', 'catering_banners', 'catering_working_hours', 'currency', 'cuisines', 'selected_cuisines', 'states', 'countries', 'selected_country'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('catering.update');
        $catering = Catering::find($id);
        if(!$catering) {
          return redirect()->route('catering.index')->with('error',trans('common.no_data'));
        }
        $currency = Setting::get('currency');
        $catering_menus = CateringMedia::where(['catering_id' => $id, 'media_type' => 'menu'])->select(['id','media_path'])->get()->toArray();
        $catering_banners = CateringMedia::where(['catering_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        $catering_working_hours = CateringWorkingHour::where('catering_id',$id)->first();
        $countries = Country::where('status','active')->get();
        $states = State::all();
        $cuisines = Cuisine::where('status','active')->get();
        $selected_country = State::with('country')->where('id',$catering->state_id)->first();
        $selected_cuisines = CateringCuisine::where('catering_id', $id)->pluck('cuisine_id')->toArray();
        // echo '<pre>'; print_r($catering_menus); die;
        return view('admin.catering.edit',compact('catering','page_title', 'catering_menus', 'catering_banners', 'catering_working_hours', 'currency', 'countries', 'cuisines', 'selected_country', 'selected_cuisines', 'states'));
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
            'food_serving:ar' => 'required',
            'address:ar' => 'required',
            'address:en' => 'required',
            'food_serving:en' => 'required',
            'link' => 'required|url|max:100',
            'banners.*' => 'image|mimes:png,jpg,jpeg|max:10000',
            'status' => 'required',
            'from_day' => 'required',
            'to_day' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
            'price' => 'required|numeric',
            'cuisines' => 'required',
            'state_id' => 'required',
        ]);

        $data = $request->all();
        $catering = Catering::find($id);

        if(!$catering) {
          return redirect()->route('catering.index')->with('error',trans('common.no_data'));
        }

        //create menus
        if($request->has('menus')){
          foreach($request->file('menus') as $menu) {
            $path = $this->saveMedia($menu,'menu');
            $catering_menus = CateringMedia::create([
                  'catering_id' => $catering->id, 
                  'media_type' => 'menu', 
                  'media_path' => $path
                ]);
          }
        }

        //create banners
        if($request->has('banners')){
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $catering_banners = CateringMedia::create([
                  'catering_id' => $catering->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          } 
        } 

        //cuisines
          CateringCuisine::where(['catering_id' => $catering->id])->delete();
          foreach ($request->cuisines as $cuisine) {
              CateringCuisine::create([
                  'catering_id' => $catering->id, 
                  'cuisine_id' => $cuisine, 
              ]);
          }  

        //working hours
        CateringWorkingHour::where('catering_id', $catering->id)->update([
          'from_day' => $request->from_day,
          'to_day' => $request->to_day,
          'from_time' => $request->from_time,
          'to_time' => $request->to_time,
        ]);

        if($catering->update($data)) {
            return redirect()->route('catering.index')->with('success',trans('catering.updated'));
        } else {
            return redirect()->route('catering.index')->with('error',trans('catering.error'));
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
        $catering = Catering::find($id);
        if($catering->delete()){
            return redirect()->route('catering.index')->with('success',trans('catering.deleted'));
        }else{
            return redirect()->route('catering.index')->with('error',trans('catering.error'));
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
        $catering= Catering::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($catering) {
        return response()->json(['success' => trans('catering.status_updated')]);
       } else {
        return response()->json(['error' => trans('catering.error')]);
       }
    }

    /**
    * Ajax for remove catering media.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function remove_media(Request $request)
    {
      // print_r($request->all()); die;
      $catering_media= CateringMedia::where('id',$request->media_id)->first();
      if(file_exists($catering_media->media_path)){
        unlink($catering_media->media_path);
      }
      if($catering_media->media_type == 'menu'){
        $messge = trans('catering.menu_deleted');
      }else{
        $messge = trans('catering.banner_deleted');
      }       
      if($catering_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('catering.error')]);
      }
    }
}
   