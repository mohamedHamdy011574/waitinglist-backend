<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CateringPackageCategory;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;
use Auth;
use App\Models\Helpers\CommonHelpers;

class CateringPackageCategoryController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware(['auth', 'vendor_subscribed','vendor_business_details']);
        $this->middleware('permission:catering-package-category-list', ['only' => ['index','show']]);
        $this->middleware('permission:catering-package-category-create', ['only' => ['create','store']]);
        $this->middleware('permission:catering-package-category-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:catering-package-category-status', ['only' => ['status']]);
    }

    public function index() {
        $page_title = trans('catering_package_categories.heading');
        return view('admin.catering_package_categories.index',compact('page_title'));
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

        $user = auth()->user();
        if($user->user_type == 'Vendor') {
          // $query = CateringAddon::query();
          $query = CateringPackageCategory::where('business_id', $user->business->id);
        } else {
          $query = CateringPackageCategory::query();
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
                            ->orWhere('status','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        if($columnName == 'name') {
            $filter = $filter->join('CateringPacakgeCategoriesTranslation', 'CateringPacakgeCategoriesTranslation.catering_pkg_cat_id', '=', 'catering_package_category.id')
            ->orderBy('CateringPacakgeCategoriesTranslation.'.$columnName, $columnSortOrder)
            ->where('CateringPacakgeCategoriesTranslation.locale',\App::getLocale())
            ->select(['catering_package_category.*']);
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
            $emp['edit'] = route("catering_package_categories.edit",$emp["id"]);
            $emp['show'] = route("catering_package_categories.show",$emp["id"]);
            $emp['delete'] = route("catering_package_categories.destroy",$emp["id"]);

            
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
        $page_title = trans('catering_package_categories.add_new');
        $business = Auth::user()->business;
        if(!$business){
          return redirect()->route('businesses.create')->with('success',trans('catering_package_category.business_details_required'));
        }

        $catering_package_category = CateringPackageCategory::where(['business_id'=>$business->id, 'status' => 'active'])->get();

        return view('admin.catering_package_categories.create',compact('page_title', 'business', 'catering_package_category'));
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
            'status' => 'required',
        ]);
        $data = $request->all();

        DB::beginTransaction();
        $data['business_id'] = Auth::user()->business->id;
        $catering_package_category = CateringPackageCategory::create($data);
        
        DB::commit();
        if('catering_package_category'){
          return redirect()->route('catering_package_categories.index')->with('success',trans('catering_package_categories.added'));
        } else {
          return redirect()->route('catering_package_categories.index')->with('error',trans('catering_package_categories.error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('catering_package_categories.show');

        $business = Auth::user()->business;
        if(!$business){
          return redirect()->route('catering_package_categories.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_package_categories  = CateringPackageCategory::find($id);
        if(!$catering_package_categories){
          return redirect()->route('catering_package_categories.index')->with('error',trans('common.no_data'));
        }

        return view('admin.catering_package_categories.show',compact('page_title','catering_package_categories','business'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page_title = trans('catering_package_categories.update');

        $business = Auth::user()->business;
        if(!$business){
          return redirect()->route('catering_package_category.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_package_categories = CateringPackageCategory::find($id);
        if(!$catering_package_categories){
          return redirect()->route('catering_package_category.index')->with('error',trans('common.no_data'));
        }
        
        return view('admin.catering_package_categories.edit',compact('page_title','catering_package_categories'));
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
            'name:ar' => 'required|min:3|max:100',
            'name:en' => 'required|min:3|max:100',
            'status' => 'required',
        ]);

        $data = $request->all();

        $catering_package_category = CateringPackageCategory::find($id);
        if(!$catering_package_category){
          return redirect()->route('catering_package_category.index')->with('error',trans('common.no_data'));
        }

        DB::beginTransaction();

        $catering_package_category = $catering_package_category->update($data);
        if('catering_package_category'){
          DB::commit();
            return redirect()->route('catering_package_categories.index')->with('success',trans('catering_package_categories.updated'));
        } else {
            return redirect()->route('catering_package_categories.index')->with('error',trans('catering_package_categories.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $catering_package_category = CateringPackageCategory::find($id);
        if(!$catering_package_category){
          return redirect()->route('catering_package_categories.index')->with('error',trans('common.no_data'));
        }
        if($catering_package_category->delete()){
            return redirect()->route('catering_package_categories.index')->with('success',trans('catering_package_categories.deleted'));
        }else{
            return redirect()->route('catering_package_categories.index')->with('error',trans('catering_package_categories.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request) {
        $catering_package_category= CateringPackageCategory::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($catering_package_category) {
        return response()->json(['success' => trans('catering_package_categories.status_updated')]);
       } else {
        return response()->json(['error' => trans('catering_package_categories.error')]);
       }
    }

}
   