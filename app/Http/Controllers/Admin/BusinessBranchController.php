<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BusinessMedia;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Country;
use App\Models\State;
use App\Models\BusinessBranch;
use App\Models\SeatingArea;
use App\Models\BusinessBranchSeating;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class BusinessBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:business-branch-list', ['only' => ['index','show']]);
        $this->middleware('permission:business-branch-create', ['only' => ['create','store']]);
        $this->middleware('permission:business-branch-edit', ['only' => ['edit','update']]);


    }

    public function index() {
        $page_title = trans('business_branches.heading');
        $seating_areas = SeatingArea::all();


      // checking subscription limit
      $add_branch_button = 1;
      $subscription = Subscription::where('vendor_id',Auth::user()->id)->first();
      $vendor = Auth::user();
      if($subscription){
          if(($subscription->branches_include > $vendor->business->restaurant_branches->count()) && ($subscription->branches_include > $vendor->business->food_truck_branches->count())  )
          {
            $add_branch_button = 1;
          }
          if(($subscription->branches_include <= $vendor->business->restaurant_branches->count()) && ($subscription->branches_include <= $vendor->business->food_truck_branches->count()))
          {
            $add_branch_button = 0;
          }
      }



        return view('admin.business_branches.index',compact('seating_areas','page_title', 'add_branch_button'));
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
          // $query = BusinessBranch::query();
          $query = BusinessBranch::where('business_id', $user->business->id);
        } else {
          $query = BusinessBranch::query();
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
                                        ->where('branch_name','like','%'.$searchValue.'%');
                                        })
                            ->orWhere('status','like','%'.$searchValue.'%')
                            ->orWhere('branch_email','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        if($columnName == 'branch_name') {
            $filter = $filter->join('business_branch_translations', 'business_branch_translations.business_branch_id', '=', 'business_branches.id')
            ->orderBy('business_branch_translations.'.$columnName, $columnSortOrder)
            ->where('business_branch_translations.locale',App::getLocale())
            ->select(['business_branches.*']);
        }else {
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }

        ## Fetch records
        $empQuery = $filter;
        $empQuery = $empQuery->offset($row)->limit($rowperpage)->get();
        $data = array();
        foreach ($empQuery as $emp) {

        ## Foreign Key Value
          $emp['branch_type'] = trans('business_branches.'.$emp["branch_type"]); 
        ## Set dynamic route for action buttons
            $emp['edit'] = route("business_branches.edit",$emp["id"]);
            $emp['show'] = route("business_branches.show",$emp["id"]);
            $emp['delete'] = route("business_branches.destroy",$emp["id"]);

            
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
        $page_title = trans('business_branches.add_new');
        // echo Auth::user()->id; die;
        $business = Auth::user()->business;
        if(!$business) {
          return redirect()->route('businesses.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_option = 1;
        if($business->catering_branch) {
          $catering_option = 0;
        }

        $subscription = Subscription::where('vendor_id',Auth::user()->id)->first();

        $countries = Country::where('status','active')->get();
        $seating_areas = SeatingArea::where('status','active')->get();
        return view('admin.business_branches.create',compact('page_title','seating_areas','countries','business', 'subscription', 'catering_option'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

      // checking subscription limit
      $subscription = Subscription::where('vendor_id',Auth::user()->id)->first();
      $vendor = Auth::user();
      if($subscription){
        if(isset($request->branch_type) && $request->branch_type != ''){
          if($request->branch_type == 'restaurant') {
            if($subscription->branches_include > $vendor->business->restaurant_branches->count())
            {
              // no action
            }
            if($subscription->branches_include <= $vendor->business->restaurant_branches->count())
            {
              return redirect()->back()->withInput()->with('error', trans('common.subscription_limit_exceed'));
            }
          }
          if($request->branch_type == 'food_truck') {
            if($subscription->branches_include > $vendor->business->food_truck_branches->count())
            {
              // no action
            }
            if($subscription->branches_include <= $vendor->business->food_truck_branches->count())
            {
              return redirect()->back()->withInput()->with('error', trans('common.subscription_limit_exceed'));
            }
          }
          if($request->branch_type == 'catering') {
            if($vendor->business->catering_branch) {
              return redirect()->back()->withInput()->with('error', trans('common.catering_have_no_branch'));
            }
          }
        }
      }

        // echo '<pre>'; print_r($request->all()); die;
        $validator= $request->validate([
            'branch_name:ar' => 'required|max:100',
            'branch_name:en' => 'required|max:100',
            'branch_type' => 'required',
            'address' => 'required|min:3',
            'latitude' => 'required',
            'longitude' => 'required',
            'state_id' => 'required|exists:states,id',
            'branch_email' => 'required|email',
            'branch_phone_number' => 'required|max:13',
            'pickups_per_hour' => 'required|numeric|min:1',
            'min_notice' => 'required_if:branch_type,catering',
            'delivery_charge' => 'required_if:branch_type,catering',
            'min_order' => 'required_if:branch_type,catering',
            'stg_area_id' => 'required_if:branch_type,catering|required_if:branch_type,restaurant',
            'status' => 'required',
        ]);
        $data = $request->all();
        $data['business_id'] = Auth::user()->business->id;
        // $state_id = State::where('name',$request->state)->first();
        // $data['state_id'] = $state_id;
        // echo '<pre>'; print_r($data); die;

        if(!isset($data['reservation_allow'])) {
          $data['reservation_allow'] = 0;
        }
        if(!isset($data['waiting_list_allow'])) {
          $data['waiting_list_allow'] = 0;
        }
        if(!isset($data['pickup_allow'])) {
          $data['pickup_allow'] = 0;
        }
        if(!isset($data['cash_payment_allow']) && !isset($data['online_payment_allow']) && !isset($data['wallet_payment_allow'])) {
            return redirect()->back()->withInput()->withError(trans('business_branches.choose_payment_option'));
        }

        if($data['branch_type'] == 'restaurant') {
          if($data['reservation_allow'] == 0 && $data['waiting_list_allow'] == 0 && $data['pickup_allow'] == 0 ) {
            return redirect()->back()->withInput()->withError(trans('subscription_packages.choose_restaurant_service'));
          }
        }

        DB::beginTransaction();
        $business_branch = BusinessBranch::create($data);

        
        if($business_branch) {
          if($request->stg_area_id) {
            //save restaurant seating
            foreach ($request->stg_area_id as $stg_area) {
                BusinessBranchSeating::create([
                    'business_branch_id' => $business_branch->id, 
                    'stg_area_id' => $stg_area, 
                ]);
            }   
          }         
        }
        DB::commit();
        
        if($business_branch) {
          return redirect()->route('business_branches.index')->with('success',trans('business_branches.added'));
        } else {
          return redirect()->route('business_branches.index')->with('error',trans('business_branches.error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('business_branches.show');
        $subscription = Subscription::where('vendor_id',Auth::user()->id)->first();

        $seating_areas = SeatingArea::where('status','active')->get();
        $selected_seating_areas = BusinessBranchSeating::where('business_branch_id', $id)->pluck('stg_area_id')->toArray();
        $business_branch = BusinessBranch::find($id);
        if(!$business_branch) {
          return redirect()->route('business_branches.index')->with('error',trans('common.no_data'));
        }

        $state_name   =  $business_branch->state->name;
        $country_name   =  $business_branch->state->country->country_name;
        
        return view('admin.business_branches.show',compact('page_title','seating_areas','selected_seating_areas', 'subscription', 'business_branch','state_name','country_name'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page_title = trans('business_branches.update');

        $subscription = Subscription::where('vendor_id',Auth::user()->id)->first();

        $countries = Country::where('status','active')->get();
        $states = State::all();
        $seating_areas = SeatingArea::where('status','active')->get();
        $selected_seating_areas = BusinessBranchSeating::where('business_branch_id', $id)->pluck('stg_area_id')->toArray();
        $business_branch = BusinessBranch::find($id);
        $country_id = $business_branch->state->country->id;
        if(!$business_branch) {
          return redirect()->route('business_branches.index')->with('error',trans('common.no_data'));
        }


        $business = Auth::user()->business;
        if(!$business) {
          return redirect()->route('businesses.create')->with('success',trans('business_branches.business_details_required'));
        }
        $catering_option = 1;
        if($business->catering_branch) {
          $catering_option = 0;
        }
        if($business_branch->branch_type == 'catering') {
          $catering_option = 1;
        }

        $selected_country = State::with('country')->where('id',$business_branch->state_id)->first();
        
        return view('admin.business_branches.edit',compact('page_title', 'states','selected_country','countries','seating_areas','selected_seating_areas','country_id', 'subscription', 'business_branch', 'catering_option'));
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
            'branch_name:ar' => 'required|max:100',
            'branch_name:en' => 'required|max:100',
            'branch_type' => 'required',
            'address' => 'required|min:3',
            'latitude' => 'required',
            'longitude' => 'required',
            'state_id' => 'required|exists:states,id',
            'branch_email' => 'required|email',
            'branch_phone_number' => 'required|max:13',
            'pickups_per_hour' => 'required|numeric|min:1',
            'min_notice' => 'required_if:branch_type,catering',
            'delivery_charge' => 'required_if:branch_type,catering',
            'min_order' => 'required_if:branch_type,catering',
            'stg_area_id' => 'required_if:branch_type,catering|required_if:branch_type,restaurant',
            'status' => 'required',
        ]);

        $data = $request->all();
        // echo '<pre>'; print_r($data);
        $business_branch = BusinessBranch::find($id);

        if($data['branch_type'] != 'restaurant'){
          $data['reservation_allow'] = 0;
          $data['waiting_list_allow'] = 0;
          $data['pickup_allow'] = 0;
          $data['pickups_per_hour'] = 0;
        }

        if(!isset($data['reservation_allow'])){
          $data['reservation_allow'] = 0;
        } 
        if(!isset($data['waiting_list_allow'])){
          $data['waiting_list_allow'] = 0;
        } 
        if(!isset($data['pickup_allow'])){
          $data['pickup_allow'] = 0;
        }        
        if(!isset($data['cash_payment_allow'])){
          $data['cash_payment_allow'] = 0;
        }
        if(!isset($data['online_payment_allow'])){
          $data['online_payment_allow'] = 0;
        }
        if(!isset($data['wallet_payment_allow'])){
          $data['wallet_payment_allow'] = 0;
        }
        // echo '<pre>'; print_r($data); die;

        if($data['cash_payment_allow'] == 0 && $data['online_payment_allow'] == 0 && $data['wallet_payment_allow'] == 0) {
            return redirect()->back()->withInput()->withError(trans('business_branches.choose_payment_option'));
        }

        if($data['branch_type'] == 'restaurant') {
          if($data['reservation_allow'] == 0 && $data['waiting_list_allow'] == 0 && $data['pickup_allow'] == 0 ) {
            return redirect()->back()->withInput()->withError(trans('subscription_packages.choose_restaurant_service'));
          }
        }

        DB::beginTransaction();
        $business_branch_update = $business_branch->update($data);
        if($business_branch) {
          

          //restaurant branch seating
          BusinessBranchSeating::where(['business_branch_id' => $business_branch->id])->delete();
          if($request->stg_area_id){
            foreach ($request->stg_area_id as $stg_area) {
                BusinessBranchSeating::create([
                    'business_branch_id' => $business_branch->id, 
                    'stg_area_id' => $stg_area, 
                ]);
            } 
          }
        }
        DB::commit();

        if($business_branch_update) {
            return redirect()->route('business_branches.index')->with('success',trans('business_branches.updated'));
        } else {
            return redirect()->route('business_branches.index')->with('error',trans('business_branches.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $restaurant = Business::find($id);
        if($business->delete()){
            return redirect()->route('business_branches.index')->with('success',trans('business_branches.deleted'));
        }else{
            return redirect()->route('business_branches.index')->with('error',trans('business_branches.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request) {
        $business_branch= BusinessBranch::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($business_branch) {
        return response()->json(['success' => trans('business_branches.status_updated')]);
       } else {
        return response()->json(['error' => trans('business_branches.error')]);
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
      $restaurant_media= BusinessMedia::where('id',$request->media_id)->first();
      if(file_exists($restaurant_media->media_path)){
        unlink($restaurant_media->media_path);
      }
      if($restaurant_media->media_type == 'menu'){
        $messge = trans('business_branches.menu_deleted');
      }else{
        $messge = trans('business_branches.banner_deleted');
      }       
      if($restaurant_media->delete()) {
        return response()->json(['success' => $messge]);
      } else {
        return response()->json(['error' => trans('business_branches.error')]);
      }
    }
}
   