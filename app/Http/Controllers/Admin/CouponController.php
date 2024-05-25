<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Business;
use App\Models\BusinessCoupon;
use App\Models\User;
use App\Models\Helpers\CommonHelpers;

class CouponController extends Controller
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
        $this->middleware('permission:coupon-list', ['only' => ['index','show']]);
        $this->middleware('permission:coupon-create', ['only' => ['create','store']]);
        $this->middleware('permission:coupon-edit', ['only' => ['edit','update']]);
    }
    public function index()
    {
        $page_title = trans('coupons.heading');
        $cuisines = Coupon::all();
        return view('admin.coupons.index',compact('cuisines','page_title'));
    }

    public function index_ajax(Request $request){
        $query = Coupon::query();
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
            $filter = $filter->where('description','like','%'.$searchValue.'%')
                            ->orWhere('name','like','%'.$searchValue.'%')
                            ->orWhere('code','like','%'.$searchValue.'%')
                            ->orWhere('discount','like','%'.$searchValue.'%');
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        
        $filter = $filter->orderBy($columnName, $columnSortOrder);
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("coupons.edit",$emp["id"]);
            $emp['show'] = route("coupons.show",$emp["id"]);
            $emp['delete'] = route("coupons.destroy",$emp["id"]);
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
        $page_title = trans('coupons.add_new');
        $restaurants = Business::where('status', 'active')->get();
        return view('admin.coupons.create',compact('page_title','restaurants'));
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
            'name' => 'required|max:255',
            'code' => 'required|min:2|max:30|unique:coupons',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|nullable|date',
            'end_date' => 'required|nullable|date|after:start_date',
            'restaurants' => 'required',
        ]);
        $data = $request->all();
        // print_r($data); die;
        $data['user_id'] = auth()->user()->id;
        $coupon = Coupon::create($data); 
        if($coupon) {
            //cuisines
            foreach ($request->restaurants as $restaurant) {
                BusinessCoupon::create([
                    'business_id' => $restaurant, 
                    'coupon_id' => $coupon->id, 
                    'type' => 'restaurant',
                ]);
            }

            //notifications
            $users = User::where('user_type','Customer')
                ->where(['verified' => 1, 'status' => 'active'])
                ->get();
            foreach ($users as $user) {
              $title = trans('coupons.notification_title');
              $body = trans('coupons.notification_body');
              $nres = $this->sendNotification($user,$title,$body,"coupon",$coupon->id);
              // echo '<pre>'; print_r($nres); die;
            }    

            return redirect()->route('coupons.index')->with('success',trans('coupons.added'));
        } else {
            return redirect()->route('coupons.index')->with('error',trans('coupons.error'));
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
        $page_title = trans('coupons.show');
        $coupon = Coupon::find($id);
        if(!$coupon) {
          return redirect()->route('coupons.index')->with('error',trans('common.no_data'));
        }
        $selected_restaurants = BusinessCoupon::where('coupon_id', $id)->pluck('business_id')->toArray();
        $restaurants = Business::where('status', 'active')->get();
        return view('admin.coupons.show',compact('coupon','page_title','selected_restaurants','restaurants'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('coupons.update');
        $coupon = Coupon::find($id);
        if(!$coupon) {
          return redirect()->route('coupons.index')->with('error',trans('common.no_data'));
        }
        $selected_restaurants = BusinessCoupon::where('coupon_id', $id)->pluck('business_id')->toArray();
        $restaurants = Business::where('status', 'active')->get();
        return view('admin.coupons.edit',compact('coupon','page_title','selected_restaurants','restaurants'));
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
            'name' => 'required|max:255',
            'code' => 'required|min:2|max:30|unique:coupons,code,'.$id,
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|nullable|date',
            'end_date' => 'required|nullable|date|after:start_date',
            'restaurants' => 'required',
        ]);
        $data = $request->all();
        $coupon = Coupon::find($id);

        //restaurants
          BusinessCoupon::where(['coupon_id' => $coupon->id])->delete();
          foreach ($request->restaurants as $restaurant) {
              BusinessCoupon::create([
                  'business_id' => $restaurant, 
                  'coupon_id' => $coupon->id, 
                  'type' => 'restaurant',
              ]);
          } 


        if($coupon->update($data)){
            return redirect()->route('coupons.index')->with('success',trans('coupons.updated'));
        } else {
            return redirect()->route('coupons.index')->with('error',trans('coupons.error'));
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
        $coupon = Coupon::find($id);
        if($coupon->delete()){
            return redirect()->route('coupons.index')->with('success',trans('coupons.deleted'));
        }else{
            return redirect()->route('coupons.index')->with('error',trans('coupons.error'));
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
        $coupon= Coupon::where('id',$request->id)
               ->update(['active'=>$request->status]);
       if($coupon) {
        return response()->json(['success' => trans('coupons.status_updated')]);
       } else {
        return response()->json(['error' => trans('coupons.error')]);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelCoupon($id)
    {
        $coupon = Coupon::where('id',$id)
               ->update(['status'=>'cancelled']);
        if($coupon){
            return redirect()->route('coupons.index')->with('success',trans('coupons.updated'));
        }else{
            return redirect()->route('coupons.index')->with('error',trans('coupons.error'));
        }
    }
}
   