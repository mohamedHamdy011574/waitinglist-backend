<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPackage;
use App\Models\Subscription;
use DB;


class SubscriptionPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:subscription-package-list', ['only' => ['index','show']]);
        $this->middleware('permission:subscription-package-create', ['only' => ['create','store']]);
        $this->middleware('permission:subscription-package-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:subscription-package-status', ['only' => ['status']]);
        $this->middleware('permission:subscription-package-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $page_title = trans('subscription_packages.heading');
        $packages = SubscriptionPackage::all();
        return view('admin.subscription_packages.index',compact('packages','page_title'));
    }

    public function index_ajax(Request $request){
        $query = SubscriptionPackage::query();
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

        ## Total number of record with filtering
        $filter= $query;
        if($searchValue != ''){
            $filter = $filter->whereHas('translation',function($query) use ($searchValue){
                        $query
                            ->where('package_name','like','%'.$searchValue.'%')
                            ->orWhere('description','like','%'.$searchValue.'%')
                            ->orWhere('branches_include','like','%'.$searchValue.'%')
                            ->orWhere('subscription_period','like','%'.$searchValue.'%')
                            ->orWhere('package_price','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%');
                         });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'package_name' || $columnName == 'description'){
            $filter = $filter->join('sub_package_translations', 'sub_package_translations.sub_package_id', '=', 'subscription_packages.id')
            ->orderBy('sub_package_translations.'.$columnName, $columnSortOrder)
            ->where('sub_package_translations.locale',\App::getLocale())
            ->select(['subscription_packages.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $service_include_array = explode(',', $emp['service_include']);
            $service_include = '';
            foreach ($service_include_array as $s) {
                $service_include .= trans('subscription_packages.'.$s) .', '; 
            }
            $emp['edit'] = route("subscription_packages.edit",$emp["id"]);
            $emp['show'] = route("subscription_packages.show",$emp["id"]);
            $emp['delete'] = route("subscription_packages.destroy",$emp["id"]);
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
        $page_title = trans('subscription_packages.add_new');
  
        return view('admin.subscription_packages.create',compact('page_title'));
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
            'package_name:en' => 'required|min:3|max:100',
            'package_name:ar' => 'required|min:3|max:100',
            'description:en' => 'required',
            'description:ar' => 'required',
            'branches_include' => 'required|numeric',
            'subscription_period' => 'required|numeric',
            'package_price' => 'required|numeric',
            'currency' => 'required',
            'status' => 'required',
        ]);


        $data = $request->all();

        if(!isset($data['for_restaurant'])) {
          $data['for_restaurant'] = 0;
        }
        if(!isset($data['for_catering'])) {
          $data['for_catering'] = 0;
        }
        if(!isset($data['for_food_truck'])) {
          $data['for_food_truck'] = 0;
        }
        if(!isset($data['reservation'])) {
          $data['reservation'] = 0;
        }
        if(!isset($data['waiting_list'])) {
          $data['waiting_list'] = 0;
        }
        if(!isset($data['pickup'])) {
          $data['pickup'] = 0;
        }

        if($data['for_restaurant'] == 0 && $data['for_catering'] == 0 && $data['for_food_truck'] == 0) {
          return redirect()->back()->withInput()->withError(trans('subscription_packages.choose_service'));
        }

        if($data['for_restaurant'] == 1) {
          if($data['reservation'] == 0 && $data['waiting_list'] == 0 && $data['pickup'] == 0 ) {
            return redirect()->back()->withInput()->withError(trans('subscription_packages.choose_restaurant_service'));
          }
        }

        // echo "<pre>"; print_r($data); 
        DB::beginTransaction();
        try {

            //checking for duplicates
            $duplicates = SubscriptionPackage::where([
                'for_restaurant' => $data['for_restaurant'],
                'for_catering' => $data['for_catering'],
                'for_food_truck' => $data['for_food_truck'],
                'reservation' => $data['reservation'],
                'waiting_list' => $data['waiting_list'],
                'pickup' => $data['pickup'],
                'branches_include' => $data['branches_include'],
                'subscription_period' => $data['subscription_period'],
            ])->get();
            // print_r($duplicates); die;
            if(count($duplicates)){
              return redirect()->back()->withInput()->withError(trans('subscription_packages.already_exists'));
            }

            $subscription_package = SubscriptionPackage::create($data);
            if($subscription_package) {
                DB::commit();
                return redirect()->route('subscription_packages.index')->with('success',trans('subscription_packages.added'));
            } else {
                return redirect()->route('subscription_packages.index')->with('error',trans('subscription_packages.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('subscription_packages.already_exists'));
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
        $page_title = trans('subscription_packages.show');
        $subscription_package = SubscriptionPackage::find($id);
        if(!$subscription_package) {
          return redirect()->route('subscription_packages.index')->with('error',trans('common.no_data'));
        }
        return view('admin.subscription_packages.show',compact('subscription_package','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('subscription_packages.update');
        $subscription_package = SubscriptionPackage::find($id);
        if(!$subscription_package) {
          return redirect()->route('subscription_packages.index')->with('error',trans('common.no_data'));
        }
        return view('admin.subscription_packages.edit',compact('subscription_package','page_title'));
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
            'package_name:en' => 'required|min:3|max:100',
            'package_name:ar' => 'required|min:3|max:100',
            'description:en' => 'required',
            'description:ar' => 'required',
            'branches_include' => 'required|numeric',
            'subscription_period' => 'required|numeric',
            'package_price' => 'required|numeric',
            'currency' => 'required',
            'status' => 'required',
        ]);

        $data = $request->all();

        if(!isset($data['for_restaurant'])) {
          $data['for_restaurant'] = 0;
        }
        if(!isset($data['for_catering'])) {
          $data['for_catering'] = 0;
        }
        if(!isset($data['for_food_truck'])) {
          $data['for_food_truck'] = 0;
        }
        if(!isset($data['reservation'])) {
          $data['reservation'] = 0;
        }
        if(!isset($data['waiting_list'])) {
          $data['waiting_list'] = 0;
        }
        if(!isset($data['pickup'])) {
          $data['pickup'] = 0;
        }

        if($data['for_restaurant'] == 0 && $data['for_catering'] == 0 && $data['for_food_truck'] == 0) {
          return redirect()->back()->withInput()->withError(trans('subscription_packages.choose_service'));
        }

        if($data['for_restaurant'] == 1){
          if($data['reservation'] == 0 && $data['waiting_list'] == 0 && $data['pickup'] == 0 ){
            return redirect()->back()->withInput()->withError(trans('subscription_packages.choose_restaurant_service'));
          }
        }
        
        $subscription_package = SubscriptionPackage::find($id);
        if(!$subscription_package) {
          return redirect()->route('subscription_packages.index')->with('error',trans('common.no_data'));
        }

        //checking for duplicates
        $duplicates = SubscriptionPackage::where([
            'for_restaurant' => $data['for_restaurant'],
            'for_catering' => $data['for_catering'],
            'for_food_truck' => $data['for_food_truck'],
            'reservation' => $data['reservation'],
            'waiting_list' => $data['waiting_list'],
            'pickup' => $data['pickup'],
            'branches_include' => $data['branches_include'],
            'subscription_period' => $data['subscription_period'],
          ])
          ->where('id','<>',$id)
          ->get();
        // print_r($duplicates); die;
        if(count($duplicates)){
          return redirect()->back()->withInput()->withError(trans('subscription_packages.already_exists'));
        }

        try {
          if($subscription_package->update($data)){
              return redirect()->route('subscription_packages.index')->with('success',trans('subscription_packages.updated'));
          } else {
              return redirect()->route('subscription_packages.index')->with('error',trans('subscription_packages.error'));
          }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('subscription_packages.already_exists'));
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
        $subscription_package = SubscriptionPackage::find($id);
        if($subscription_package->delete()){
            return redirect()->route('subscription_packages.index')->with('success',trans('subscription_packages.deleted'));
        }else{
            return redirect()->route('subscription_packages.index')->with('error',trans('subscription_packages.error'));
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
        $subscription_package= SubscriptionPackage::where('id',$request->id)->first();
        // if(Subscription::where('package_id',$request->id)->get()->count()){
        //   return response()->json(['error' => trans('subscription_packages.cant_inactive')]);
        // }
        $subscription_package->update(['status'=>$request->status]);
       if($subscription_package) {
        return response()->json(['success' => trans('subscription_packages.status_updated')]);
       } else {
        return response()->json(['error' => trans('subscription_packages.error')]);
       }
    }
}
   