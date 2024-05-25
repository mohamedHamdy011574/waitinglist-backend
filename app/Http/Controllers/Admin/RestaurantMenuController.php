<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\RestaurantMenu;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\Helpers\CommonHelpers;

class RestaurantMenuController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:menu-list', ['only' => ['index','show']]);
        $this->middleware('permission:menu-create', ['only' => ['create','store']]);
        $this->middleware('permission:menu-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:menu-status', ['only' => ['status']]);
    }

    public function index() {
        $page_title = trans('restaurant_menus.heading');
        $currency = Setting::get('currency');
        return view('admin.restaurant_menus.index',compact('page_title','currency'));
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
          // $query = RestaurantMenu::query();
          $query = RestaurantMenu::where('business_id', $user->business->id);
        } else {
          $query = RestaurantMenu::query();
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
                                        ->where('name','like','%'.$searchValue.'%');
                                        })
                            ->orWhere('price','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        if($columnName == 'name') {
            $filter = $filter->join('restaurant_menu_translations', 'restaurant_menu_translations.restaurant_menu_id', '=', 'restaurant_menus.id')
            ->orderBy('restaurant_menu_translations.'.$columnName, $columnSortOrder)
            ->where('restaurant_menu_translations.locale',\App::getLocale())
            ->select(['restaurant_menus.*']);
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
            $emp['edit'] = route("restaurant_menus.edit",$emp["id"]);
            $emp['show'] = route("restaurant_menus.show",$emp["id"]);
            $emp['delete'] = route("restaurant_menus.destroy",$emp["id"]);
            $emp['menu_category'] = RestaurantMenu::find($emp["id"])->menu_category->name;

            
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
        $page_title = trans('restaurant_menus.add_new');
        // echo Auth::user()->id; die;
        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        $menu_categories = $business->menus_categories;
        if(!$business) {
          return redirect()->route('businesses.create')->with('success',trans('restaurant_menus.business_details_required'));
        }
        return view('admin.restaurant_menus.create',compact('page_title','currency', 'business', 'menu_categories'));
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
            'name:ar' => 'required|min:3|max:100',
            'name:en' => 'required|min:3|max:100',
            'description:ar' => 'required|min:5',
            'description:en' => 'required|min:5',
            'menu_category' => 'required',
            'price' => 'required',
            'currency' => 'required',
            'menu_item_photo' => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'status' => 'required',
        ]);
        $data = $request->all();
        $data['business_id'] = Auth::user()->business->id;
        $data['menu_category_id'] = $request->menu_category;
        $data['currency'] = Setting::get('currency');

        DB::beginTransaction();
        try {
            //create banners
            if($request->has('menu_item_photo')) {
                $path = $this->saveMedia($request->file('menu_item_photo'),'menu_item_photo');
                $data['menu_item_photo'] = $path;
            }

            $restaurant_menu = RestaurantMenu::create($data);

            if($restaurant_menu) {
              DB::commit();
              return redirect()->route('restaurant_menus.index')->with('success',trans('restaurant_menus.added'));
            } else {
              return redirect()->route('restaurant_menus.index')->with('error',trans('restaurant_menus.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('restaurant_menus.already_exists'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('restaurant_menus.show');

        $business = Auth::user()->business;
        if(!$business){
          return redirect()->route('restaurant_menus.create')->with('success',trans('business_branches.business_details_required'));
        }
        $currency = Setting::get('currency');

        $restaurant_menu = RestaurantMenu::find($id);
        if(!$restaurant_menu){
          return redirect()->route('restaurant_menus.index')->with('error',trans('common.no_data'));
        }
        $menu_categories = $business->menus_categories;
        
        return view('admin.restaurant_menus.show',compact('page_title','restaurant_menu','business','currency','menu_categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page_title = trans('restaurant_menus.update');

        $business = Auth::user()->business;
        if(!$business){
          return redirect()->route('restaurant_menus.create')->with('success',trans('business_branches.business_details_required'));
        }
        $currency = Setting::get('currency');

        $restaurant_menu = RestaurantMenu::find($id);
        if(!$restaurant_menu){
          return redirect()->route('restaurant_menus.index')->with('error',trans('common.no_data'));
        }
        $menu_categories = $business->menus_categories;
        
        return view('admin.restaurant_menus.edit',compact('page_title','restaurant_menu','business','currency','menu_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
      print_r($request->all());
        $validator= $request->validate([
            'name:ar' => 'required|min:3|max:100',
            'name:en' => 'required|min:3|max:100',
            'description:ar' => 'required|min:5',
            'description:en' => 'required|min:5',
            'menu_category' => 'required',
            'price' => 'required',
            'currency' => 'required',
            'menu_item_photo' => 'image|mimes:png,jpg,jpeg|max:10000',
            'status' => 'required',
        ]);

        $data = $request->all();
        // echo '<pre>'; print_r($data); die;



        $restaurant_menu = RestaurantMenu::find($id);
        if(!$restaurant_menu){
          return redirect()->route('restaurant_menus.index')->with('error',trans('common.no_data'));
        }

        DB::beginTransaction();
        try {
            //create menu_item_photo
            if($request->has('menu_item_photo')) {
                $path = $this->saveMedia($request->file('menu_item_photo'),'banner');
                $data['menu_item_photo'] = $path;
                unlink($restaurant_menu->menu_item_photo);
            }
            $data['menu_category_id'] = $data['menu_category'];
            $restaurant_menu_update = $restaurant_menu->update($data);
            DB::commit();

            if($restaurant_menu_update) {
                return redirect()->route('restaurant_menus.index')->with('success',trans('restaurant_menus.updated'));
            } else {
                return redirect()->route('restaurant_menus.index')->with('error',trans('restaurant_menus.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('restaurant_menus.already_exists'));
        }    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $restaurant_menu = RestaurantMenu::find($id);
        if(!$restaurant_menu){
          return redirect()->route('restaurant_menus.index')->with('error',trans('common.no_data'));
        }
        if($restaurant_menu->delete()){
            return redirect()->route('restaurant_menus.index')->with('success',trans('restaurant_menus.deleted'));
        }else{
            return redirect()->route('restaurant_menus.index')->with('error',trans('restaurant_menus.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request) {
        $restaurant_menu= RestaurantMenu::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($restaurant_menu) {
        return response()->json(['success' => trans('restaurant_menus.status_updated')]);
       } else {
        return response()->json(['error' => trans('restaurant_menus.error')]);
       }
    }
}
   