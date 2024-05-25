<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubscriptionPackage;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Mail\VendorRegistered;
use Mail;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:vendor-list', ['only' => ['index','show']]);
        $this->middleware('permission:vendor-create', ['only' => ['create','store']]);
        $this->middleware('permission:vendor-edit', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $page_title = trans('vendors.heading');
        $vendors = User::where('user_type','Vendor')->get();
        return view('admin.vendors.index',compact('vendors','page_title'));
    }

    public function index_ajax(Request $request){
        $query = User::where('user_type', 'Vendor');
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
            $filter = $filter->where(function($q)use ($searchValue) {
                          $q->orWhere('first_name','like','%'.$searchValue.'%')
                            ->orWhere('last_name','like','%'.$searchValue.'%')
                            ->orWhere('email','like','%'.$searchValue.'%')
                            ->orWhere('phone_number','like','%'.$searchValue.'%')->orwhereHas('business', function($qq) use ($searchValue) {
                              $qq->whereHas('translation', function($qqq) use ($searchValue) {
                                  $qqq->where('brand_name','like','%'.$searchValue.'%');
                              });
                            });
                     });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons          
            $emp['edit'] = route("vendors.edit",$emp["id"]);
            $emp['show'] = route("vendors.show",$emp["id"]);
            $emp['delete'] = route("vendors.destroy",$emp["id"]);
            $emp['brand_name'] = (@$emp->business->brand_name) ? @$emp->business->brand_name : '-';
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
      $page_title = trans('vendors.add_new');
      $subscription_packages = SubscriptionPackage::where(['status' => 'active'])->latest()->get();
      return view('admin.vendors.create',compact('page_title', 'subscription_packages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // echo "<pre>"; print_r($request->all()); die;

      // echo Carbon::today()->addYears(2); die;
        // echo "<pre>"; print_r($request->all()); die;
        $validator= $request->validate([
            'first_name' => ['required', 'string', 'min:3', 'max:100'],
            'last_name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'phone_number' => ['required','numeric','digits_between:8,12','unique:users,id'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
            'subscription_package' => ['required', 'exists:subscription_packages,id'],
        ]);

        if (is_numeric($request['first_name'])) {
          throw ValidationException::withMessages(['first_name' => trans('common.invalid_first_name')]);
        }
        if (is_numeric($request['last_name'])) {
          throw ValidationException::withMessages(['first_name' => trans('common.invalid_last_name')]);
        }
        
        $password = $request->password;
        $request->request->add([
          'user_type'=> 'Vendor',
          'password' => Hash::make($request->password),
          'verified' => 1,
        ]);
        $data = $request->all();
        
        // echo '<pre>'; print_r($data); die;
        DB::beginTransaction();
        $vendor = User::create($data);
        $vendor->email_verified_at = date('Y-m-d');
        $vendor->save();  
        $role = Role::where('name','Vendor')->first();
        $vendor->assignRole([$role->id]);
        if($vendor) {

          //saving subscription
          $subscription_data = [];
          $r_package = SubscriptionPackage::find($request->subscription_package);
          if(!$r_package) {
            return redirect()->back()->withInput()->with('error',trans('common.no_data'));
          }
          $subscription_data[] = [
            'sub_package_id' => $request->subscription_package,
            'vendor_id' => $vendor->id,

            'for_restaurant' => $r_package->for_restaurant,
            'for_catering' => $r_package->for_catering,
            'for_food_truck' => $r_package->for_food_truck,
            'reservation' => $r_package->reservation,
            'waiting_list' => $r_package->waiting_list,
            'pickup' => $r_package->pickup,
            'branches_include' => $r_package->branches_include,
            'subscription_period' => $r_package->subscription_period,
            'package_price' => $r_package->package_price,
            'currency' => $r_package->currency,
            'purchase_date' => date('Y-m-d'),
            'package_end_date' => Carbon::today()->addYears($r_package->subscription_period),
            'payment_mode' => 'manual',
            'payment_status' => 'paid',
          ];

          Subscription::insert($subscription_data);


          //sending credentials email
          $details = [
            'name' => $vendor->first_name,
            'email' => $vendor->email,
            'password' => $password,
          ];
          Mail::to($vendor->email)->send(new VendorRegistered($details));
  
          DB::commit();
          return redirect()->route('vendors.index')->with('success',trans('vendors.added'));
        } else {
          DB::rollback();
          return redirect()->route('vendors.index')->with('error',trans('vendors.error'));
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
        $page_title = trans('vendors.show');
        $vendor = User::find($id);

        if(!$vendor) {
          return redirect()->route('vendors.index')->with('error',trans('common.no_data'));
        }
        
        $subscription_packages = SubscriptionPackage::where(['status' => 'active'])->latest()->get();

        $subscription = Subscription::where(['vendor_id' => $vendor->id])->first();
        if(!$subscription) {
          $subscription = '';
        }

        return view('admin.vendors.show',compact('vendor', 'page_title', 'subscription_packages', 'subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('vendors.update');
        $vendor = User::find($id);
        if(!$vendor) {
          return redirect()->route('vendors.index')->with('error',trans('common.no_data'));
        }
        
        $subscription_packages = SubscriptionPackage::where(['status' => 'active'])->latest()->get();

        $subscription = Subscription::where(['vendor_id' => $vendor->id])->first();
        if(!$subscription) {
          $subscription = '';
        }
        // echo '<pre>'; print_r($restaurant_subscription); die;
        return view('admin.vendors.edit',compact('vendor','page_title', 'subscription_packages', 'subscription'));
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
            'first_name' => ['required', 'string', 'min:3', 'max:100'],
            'last_name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,id'],
            'phone_number' => ['required','numeric','digits_between:8,12','unique:users,id'],
            'password' => ['sometimes', 'nullable', 'string', 'min:6','max:20','confirmed'],
            'subscription_package' => ['required', 'exists:subscription_packages,id'],
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'user_type'=> 'Vendor',
            'verified' => 1,
        ];

        if($request->has('password') && $request->password != '') { 
          // $request->request->add([
          //   'password' => Hash::make($request->password),
          // ]);
          $data['password'] = Hash::make($request->password);
        }

        // $data = $request->all();

        $vendor = User::find($id);

        // echo '<pre>'; print_r($data); die;

        DB::beginTransaction();
        if($vendor) {
          $vendor->update($data);

          //saving subscription
          Subscription::where('vendor_id',$vendor->id)->delete();
          $subscription_data = [];
            $r_package = SubscriptionPackage::find($request->subscription_package);
            $subscription_data[] = [
              'sub_package_id' => $request->subscription_package,
              'vendor_id' => $vendor->id,
              'for_restaurant' => $r_package->for_restaurant,
              'for_catering' => $r_package->for_catering,
              'for_food_truck' => $r_package->for_food_truck,
              'reservation' => $r_package->reservation,
              'waiting_list' => $r_package->waiting_list,
              'pickup' => $r_package->pickup,
              'branches_include' => $r_package->branches_include,
              'subscription_period' => $r_package->subscription_period,
              'package_price' => $r_package->package_price,
              'currency' => $r_package->currency,
              'purchase_date' => date('Y-m-d'),
              'package_end_date' => Carbon::today()->addYears($r_package->subscription_period),
              'payment_mode' => 'manual',
              'payment_status' => 'paid',
            ];

          Subscription::insert($subscription_data);

          DB::commit();
          return redirect()->route('vendors.index')->with('success',trans('vendors.updated'));
        }else{
          DB::rollback();
          return redirect()->route('vendors.index')->with('error',trans('vendors.error'));
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
        $vendor = User::find($id);
        if($vendor->delete()){
            return redirect()->route('vendors.index')->with('success',trans('vendors.deleted'));
        }else{
            return redirect()->route('vendors.index')->with('error',trans('vendors.error'));
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
        $vendor= User::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($vendor) {
        return response()->json(['success' => trans('vendors.status_updated')]);
       } else {
        return response()->json(['error' => trans('vendors.error')]);
       }
    }
}
   