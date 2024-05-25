<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CateringPlan;
use App\Models\CateringPlanMedia;
use App\Models\Subscription;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;
use Auth;
use App\Models\Helpers\CommonHelpers;

class CateringPlanController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:catering-plan-list', ['only' => ['index','show']]);
        $this->middleware('permission:catering-plan-create', ['only' => ['create','store']]);
        $this->middleware('permission:catering-plan-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:catering-plan-status', ['only' => ['status']]);
    }

    public function index() {
        $page_title = trans('catering_plans.heading');
        $currency = Setting::get('currency');
        return view('admin.catering_plans.index',compact('page_title','currency'));
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
        if($user->user_type == 'Vendor') {
          // $query = CateringPlan::query();
          $query = CateringPlan::where('business_id', $user->business->id);
        } else {
          $query = CateringPlan::query();
        }
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter= $query;

        if($searchValue != ''){
        $filter = $filter->where(function($q)use ($searchValue) {
                            $q->whereHas('translation',function($query) use ($searchValue){
                                 $query
                                        ->where('plan_name','like','%'.$searchValue.'%');
                                        })
                            ->orWhere('status','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        if($columnName == 'plan_name') {
            $filter = $filter->join('catering_plan_translations', 'catering_plan_translations.catering_plan_id', '=', 'catering_plans.id')
            ->orderBy('catering_plan_translations.'.$columnName, $columnSortOrder)
            ->where('catering_plan_translations.locale',\App::getLocale())
            ->select(['catering_plans.*']);
        }else {
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }

        ## Fetch records
        $empQuery = $filter;
        $empQuery = $empQuery->offset($row)->limit($rowperpage)->get();
        $data = array();
        foreach ($empQuery as $emp) {

        ## Foreign Key Value 
        ## Set dynamic route for action buttons
          $emp['person_to_served'] = $emp["persons_served_min"].' - '.$emp["persons_served_max"];
            $emp['edit'] = route("catering_plans.edit",$emp["id"]);
            $emp['show'] = route("catering_plans.show",$emp["id"]);
            $emp['delete'] = route("catering_plans.destroy",$emp["id"]);

            
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
        $page_title = trans('catering_plans.add_new');
        // echo Auth::user()->id; die;
        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('businesses.create')->with('success',trans('catering_plans.business_details_required'));
        }
        return view('admin.catering_plans.create',compact('page_title','currency', 'business'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // echo '<pre>'; print_r($request->all()); die;
        $validator= $request->validate([
            'plan_name:ar' => 'required|min:3|max:100',
            'plan_name:en' => 'required|min:3|max:100',
            'description:ar' => 'required|min:5',
            'description:en' => 'required|min:5',
            'food_serving:ar' => 'required|min:5',
            'food_serving:en' => 'required|min:5',
            'persons_served_min' => 'required|numeric|digits_between:1,10000',
            'persons_served_max' => 'required|numeric|digits_between:1,10000',
            'from_time' => 'required',
            'to_time' => 'required',
            'plan_rate' => 'required',
            'currency' => 'required',
            'status' => 'required',
            'banners.*' => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'setup_time' => 'required_if:served_off_premises,1|numeric|nullable',
            'max_time' => 'required_if:served_off_premises,1|numeric|nullable',
        ]);
        $data = $request->all();
        $data['business_id'] = Auth::user()->business->id;



        if(!isset($data['sunday_serving']) && !isset($data['monday_serving']) && !isset($data['tuesday_serving']) && !isset($data['wednesday_serving']) && !isset($data['thursday_serving']) && !isset($data['friday_serving']) && !isset($data['saturday_serving'])){
          return redirect()->back()->withInput()->with('error',trans('catering_plans.choose_one_serving_day'));
        }
        if(!isset($data['served_in_restaurant']) && !isset($data['served_off_premises'])) {
          return redirect()->back()->withInput()->with('error',trans('catering_plans.choose_one_plan'));
        }
        // $state_id = State::where('name',$request->state)->first();
        // $data['state_id'] = $state_id;
        // echo '<pre>'; print_r($data); die;


        DB::beginTransaction();
        $catering_plan = CateringPlan::create($data);

        //create banners
        if($catering_plan) {
          if($request->has('banners')) {
            foreach($request->file('banners') as $banner) {
              $path = $this->saveMedia($banner,'banner');
              $catering_banners = CateringPlanMedia::create([
                    'catering_plan_id' => $catering_plan->id, 
                    'media_type' => 'banner', 
                    'media_path' => $path
                  ]);
            }  
          }
        }

        DB::commit();
        
        if($catering_plan) {
          return redirect()->route('catering_plans.index')->with('success',trans('catering_plans.added'));
        } else {
          return redirect()->route('catering_plans.index')->with('error',trans('catering_plans.error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('catering_plans.update');

        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('catering_plans.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_plan = CateringPlan::find($id);
        if(!$catering_plan){
          return redirect()->route('catering_plans.index')->with('error',trans('common.no_data'));
        }

        $catering_plan_banners = CateringPlanMedia::where(['catering_plan_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        
        return view('admin.catering_plans.show',compact('page_title','catering_plan','business','currency', 'catering_plan_banners'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page_title = trans('catering_plans.update');

        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('catering_plans.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_plan = CateringPlan::find($id);
        if(!$catering_plan){
          return redirect()->route('catering_plans.index')->with('error',trans('common.no_data'));
        }

        $catering_plan_banners = CateringPlanMedia::where(['catering_plan_id' => $id, 'media_type' => 'banner'])->select(['id','media_path'])->get()->toArray();
        
        return view('admin.catering_plans.edit',compact('page_title','catering_plan','business','currency', 'catering_plan_banners'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
      // print_r($request->all());
        $validator= $request->validate([
            'plan_name:ar' => 'required|min:3|max:100',
            'plan_name:en' => 'required|min:3|max:100',
            'description:ar' => 'required|min:5',
            'description:en' => 'required|min:5',
            'food_serving:ar' => 'required|min:5',
            'food_serving:en' => 'required|min:5',
            'persons_served_min' => 'required|numeric|digits_between:1,10000',
            'persons_served_max' => 'required|numeric|digits_between:1,10000',
            'from_time' => 'required',
            'to_time' => 'required',
            'plan_rate' => 'required',
            'currency' => 'required',
            'status' => 'required',
            'banners.*' => 'image|mimes:png,jpg,jpeg|max:10000',
            'setup_time' => 'required_if:served_off_premises,1|numeric|nullable',
            'max_time' => 'required_if:served_off_premises,1|numeric|nullable',
        ]);

        $data = $request->all();
        // echo '<pre>'; print_r($data); die;



        $catering_plan = CateringPlan::find($id);
        if(!$catering_plan){
          return redirect()->route('catering_plans.index')->with('error',trans('common.no_data'));
        }

        if(!isset($data['sunday_serving']) && !isset($data['monday_serving']) && !isset($data['tuesday_serving']) && !isset($data['wednesday_serving']) && !isset($data['thursday_serving']) && !isset($data['friday_serving']) && !isset($data['saturday_serving'])){
          return redirect()->back()->withInput()->with('error',trans('catering_plans.choose_one_serving_day'));
        }
        if(!isset($data['served_in_restaurant']) && !isset($data['served_off_premises'])) {
          return redirect()->back()->withInput()->with('error',trans('catering_plans.choose_one_plan'));
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

        if(!isset($data['served_in_restaurant'])){
          $data['served_in_restaurant'] = 0;
        } if(!isset($data['served_off_premises'])){
          $data['served_off_premises'] = 0;
          $data['setup_time'] = null;
          $data['max_time'] = null;
        } 
        // echo '<pre>'; print_r($data); die;

        DB::beginTransaction();
        //create banners
        if($request->has('banners')){
          foreach($request->file('banners') as $banner) {
            $path = $this->saveMedia($banner,'banner');
            $catering_plans = CateringPlanMedia::create([
                  'catering_plan_id' => $request->id, 
                  'media_type' => 'banner', 
                  'media_path' => $path
                ]);
          } 
        } 

        $catering_plan_update = $catering_plan->update($data);
        DB::commit();

        if($catering_plan_update) {
            return redirect()->route('catering_plans.index')->with('success',trans('catering_plans.updated'));
        } else {
            return redirect()->route('catering_plans.index')->with('error',trans('catering_plans.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $catering_plan = CateringPlan::find($id);
        if(!$catering_plan){
          return redirect()->route('catering_plans.index')->with('error',trans('common.no_data'));
        }
        if($catering_plan->delete()){
            return redirect()->route('catering_plans.index')->with('success',trans('catering_plans.deleted'));
        }else{
            return redirect()->route('catering_plans.index')->with('error',trans('catering_plans.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request) {
        $catering_plan= CateringPlan::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($catering_plan) {
        return response()->json(['success' => trans('catering_plans.status_updated')]);
       } else {
        return response()->json(['error' => trans('catering_plans.error')]);
       }
    }

    /**
    * Ajax for remove catering media.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function remove_media(Request $request) {
      // print_r($request->all()); die;
      $catering_plan_media= CateringPlanMedia::where('id',$request->media_id)->first();
      if(file_exists($catering_plan_media->media_path)){
        unlink($catering_plan_media->media_path);
      }
      $messge = trans('catering_plans.banner_deleted');       
      if($catering_plan_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('catering_plans.error')]);
      }
    }
}
   