<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CateringPlan;
use App\Models\CateringAddon;
use App\Models\CateringAddonMedia;
use App\Models\CateringPlanAddon;
use App\Models\Subscription;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\APP;
use App\Models\Helpers\CommonHelpers;

class CateringAddonController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware(['auth', 'vendor_subscribed','vendor_business_details']);
        $this->middleware('permission:catering-addon-list', ['only' => ['index','show']]);
        $this->middleware('permission:catering-addon-create', ['only' => ['create','store']]);
        $this->middleware('permission:catering-addon-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:catering-addon-status', ['only' => ['status']]);
    }

    public function index() {
        $page_title = trans('catering_addons.heading');
        $currency = Setting::get('currency');
        return view('admin.catering_addons.index',compact('page_title','currency'));
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
          // $query = CateringAddon::query();
          $query = CateringAddon::where('business_id', $user->business->id);
        } else {
          $query = CateringAddon::query();
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
                                        ->where('addon_name','like','%'.$searchValue.'%');
                                        })
                            ->orWhere('status','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        if($columnName == 'addon_name') {
            $filter = $filter->join('catering_addon_translations', 'catering_addon_translations.catering_addon_id', '=', 'catering_addons.id')
            ->orderBy('catering_addon_translations.'.$columnName, $columnSortOrder)
            ->where('catering_addon_translations.locale',App::getLocale())
            ->select(['catering_addons.*']);
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
            $emp['edit'] = route("catering_addons.edit",$emp["id"]);
            $emp['show'] = route("catering_addons.show",$emp["id"]);
            $emp['delete'] = route("catering_addons.destroy",$emp["id"]);

            
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
        $page_title = trans('catering_addons.add_new');
        // echo Auth::user()->id; die;
        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('businesses.create')->with('success',trans('catering_addons.business_details_required'));
        }

        // $catering_plans = CateringPlan::where(['business_id'=>$business->id, 'status' => 'active'])->get();

        return view('admin.catering_addons.create',compact('page_title','currency', 'business'));
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
            // 'catering_plans' => 'required',
            'addon_name:ar' => 'required|min:3|max:100',
            'addon_name:en' => 'required|min:3|max:100',
            'description:ar' => 'required|min:3|max:500',
            'description:en' => 'required|min:3|max:500',
            'addon_rate' => 'required',
            'currency' => 'required',
            'status' => 'required',
        ]);
        $data = $request->all();

        DB::beginTransaction();
        $data['business_id'] = Auth::user()->business->id;
        $catering_addon = CateringAddon::create($data);
        
        if($catering_addon) {
          // foreach ($data['catering_plans'] as $cp) {
          //   CateringPlanAddon::updateOrCreate(['catering_plan_id' => $cp, 'catering_addon_id' => $catering_addon->id]);
          // }
          DB::commit();
          return redirect()->route('catering_addons.index')->with('success',trans('catering_addons.added'));
        } else {
          return redirect()->route('catering_addons.index')->with('error',trans('catering_addons.error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('catering_addons.update');

        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('catering_addons.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_addon = CateringAddon::find($id);
        if(!$catering_addon){
          return redirect()->route('catering_addons.index')->with('error',trans('common.no_data'));
        }

        // $selected_catering_plans = CateringPlanAddon::where('catering_addon_id', $id)->pluck('catering_plan_id')->toArray();
        // $catering_plans = CateringPlan::where(['business_id'=>$business->id, 'status' => 'active'])->get();

        return view('admin.catering_addons.show',compact('page_title','catering_addon','business','currency'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page_title = trans('catering_addons.update');

        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('catering_addons.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_addon = CateringAddon::find($id);
        if(!$catering_addon){
          return redirect()->route('catering_addons.index')->with('error',trans('common.no_data'));
        }

       
        
        return view('admin.catering_addons.edit',compact('page_title','catering_addon','business','currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
      // print_r($request->all()); die;
        $validator= $request->validate([
            // 'catering_plans' => 'required',
            'addon_name:ar' => 'required|min:3|max:100',
            'addon_name:en' => 'required|min:3|max:100',
            'addon_rate' => 'required',
            'currency' => 'required',
            'status' => 'required',
        ]);

        $data = $request->all();
        // echo '<pre>'; print_r($data); die;

        $catering_addon = CateringAddon::find($id);
        if(!$catering_addon){
          return redirect()->route('catering_addons.index')->with('error',trans('common.no_data'));
        }

        

        DB::beginTransaction();

        $catering_addon_update = $catering_addon->update($data);

        if($catering_addon_update) {
          // CateringPlanAddon::where('catering_addon_id',$id)->delete();
          // foreach ($data['catering_plans'] as $cp) {
          //   CateringPlanAddon::create(['catering_plan_id' => $cp, 'catering_addon_id' => $id]);
          // }
          DB::commit();

            return redirect()->route('catering_addons.index')->with('success',trans('catering_addons.updated'));
        } else {
            return redirect()->route('catering_addons.index')->with('error',trans('catering_addons.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $catering_addon = CateringAddon::find($id);
        if(!$catering_addon){
          return redirect()->route('catering_addons.index')->with('error',trans('common.no_data'));
        }
        if($catering_addon->delete()){
            return redirect()->route('catering_addons.index')->with('success',trans('catering_addons.deleted'));
        }else{
            return redirect()->route('catering_addons.index')->with('error',trans('catering_addons.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request) {
        $catering_addon= CateringAddon::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($catering_addon) {
        return response()->json(['success' => trans('catering_addons.status_updated')]);
       } else {
        return response()->json(['error' => trans('catering_addons.error')]);
       }
    }

}
   