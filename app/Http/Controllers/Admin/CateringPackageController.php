<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CateringPackage;
use App\Models\CateringPackageCategory;
use App\Models\CateringPackageMedia;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Input;
use Auth;
use App\Models\Helpers\CommonHelpers;

class CateringPackageController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware(['auth', 'vendor_subscribed','vendor_business_details']);
        $this->middleware('permission:catering-package-list', ['only' => ['index','show']]);
        $this->middleware('permission:catering-package-create', ['only' => ['create','store']]);
        $this->middleware('permission:catering-package-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:catering-package-status', ['only' => ['status']]);
    }

    public function index() {
        $page_title = trans('catering_packages.heading');
        $currency = Setting::get('currency');
        return view('admin.catering_packages.index',compact('page_title','currency'));
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
          $query = CateringPackage::where('business_id', $user->business->id);
        } else {
          $query = CateringPackage::query();
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
                                        ->where('package_name','like','%'.$searchValue.'%');
                                        })
                            ->orWhere('status','like','%'.$searchValue.'%')
                            ->orWhere('person_serve','like','%'.$searchValue.'%')
                            ->orWhere('price','like','%'.$searchValue.'%');
                     });
        }

        $filter_data=$filter->count();
        $totalRecordwithFilter = $filter_data;

        if($columnName == 'package_name') {
            $filter = $filter->join('CateringPacakgeTranslation', 'CateringPacakgeTranslation.catering_pkg_id', '=', 'catering_package.id')
            ->orderBy('CateringPacakgeTranslation.'.$columnName, $columnSortOrder)
            ->where('CateringPacakgeTranslation.locale',\App::getLocale())
            ->select(['catering_package.*']);
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
           
             $emp['image'] = '<img src="'.asset(CateringPackageMedia::where(['catering_pkg_id'=> $emp["id"]])->first()->image).'" style="width:50px">';
            $emp['edit'] = route("catering_packages.edit",$emp["id"]);
            $emp['show'] = route("catering_packages.show",$emp["id"]);
            $emp['delete'] = route("catering_packages.destroy",$emp["id"]);
            $emp['catering_pkg_cat_id'] = $emp->category->name;

            
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
        $page_title = trans('catering_packages.add_new');
        $business = Auth::user()->business;
        $catering_package_category = CateringPackageCategory::where(['business_id' => $business->id, 'status' =>'active'])->get();
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('businesses.create')->with('success',trans('catering_package_category.business_details_required'));
        }

        $catering_package = CateringPackage::where(['business_id'=>$business->id, 'status' => 'active'])->get();

        return view('admin.catering_packages.create',compact('page_title', 'business', 'catering_package_category','catering_package','currency'));
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

            'package_name:ar'        => 'required|min:3|max:100',
            'package_name:en'        => 'required|min:3|max:100',
            'food_serving:ar'        => 'required|min:3',
            'food_serving:en'        => 'required|min:3',
            'status'                 => 'required',
            'person_serve'           => 'required|numeric',
            'price'                  => 'required|numeric',
            'setup_time'             => 'required',
            'max_time'               => 'required|numeric',
            'max_time_unit'          => 'required',
            'image.*'                => 'required|image|mimes:png,jpg,jpeg|max:10000',
            'category_name'          => 'required',
        ]);
        $data = $request->all();

        DB::beginTransaction();
        $data['business_id'] = Auth::user()->business->id;
        $data['catering_pkg_cat_id']   =  $request->category_name;
        $catering_package_category = CateringPackage::create($data);

        //create image\
          foreach($request->file('image') as $catering_package_image) {
            $path = $this->saveMedia($catering_package_image,'catering_package_image');
            $catering_image = CateringPackageMedia::create([
                  'catering_pkg_id' => $catering_package_category->id, 
                  'image' => $path,
                ]);
          }


        DB::commit();
        if('catering_package_category'){
          return redirect()->route('catering_packages.index')->with('success',trans('catering_packages.added'));
        } else {
          return redirect()->route('catering_packages.index')->with('error',trans('catering_packages.error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('catering_packages.show');
        $business = Auth::user()->business;
        $currency = Setting::get('currency');
        if(!$business){
          return redirect()->route('catering_packages.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_package_image = CateringPackageMedia::where('catering_pkg_id',$id)->select(['id','image'])->get()->toArray();
        $catering_package  = CateringPackage::find($id);
        if(!$catering_package){
          return redirect()->route('catering_packages.index')->with('error',trans('common.no_data'));
        }

        return view('admin.catering_packages.show',compact('page_title','catering_package','business','catering_package_image','currency'));
    }

    /**e
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page_title = trans('catering_packages.update');
        $business = Auth::user()->business;
        $catering_package_category = CateringPackageCategory::where(['business_id' => $business->id, 'status' =>'active'])->get();
        $catering_package_image = CateringPackageMedia::where(['catering_pkg_id'=>$id])->select(['id','image'])->get()->toArray();
        if(!$business){
          return redirect()->route('catering_package_category.create')->with('success',trans('business_branches.business_details_required'));
        }

        $catering_package = CateringPackage::find($id);
         $currency = Setting::get('currency');
        if(!$catering_package){
          return redirect()->route('catering_package_category.index')->with('error',trans('common.no_data'));
        }
        
        return view('admin.catering_packages.edit',compact('page_title','catering_package','catering_package_category','catering_package_image','currency'));
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
            'package_name:ar'        => 'required|min:3|max:100',
            'package_name:en'        => 'required|min:3|max:100',
            'food_serving:ar'        => 'required|min:3',
            'food_serving:en'        => 'required|min:3',
            'status'                 => 'required',
            'person_serve'           => 'required|numeric',
            'price'                  => 'required|numeric',
            'setup_time'             => 'required',
            'max_time'               => 'required|numeric',
            'max_time_unit'          => 'required',
            'image.*'                => 'sometimes|required|image|mimes:png,jpg,jpeg|max:10000',
            'category_name'          => 'required',
        ]);

        $data = $request->all();

        $catering_package_category = CateringPackage::find($id);
        if(!$catering_package_category){
          return redirect()->route('catering_packages.index')->with('error',trans('common.no_data'));
        }

        DB::beginTransaction();
        //create image\
        if($request->image){
          foreach($request->file('image') as $catering_package_image) {
          $path = $this->saveMedia($catering_package_image,'catering_package_image');
          $catering_image = CateringPackageMedia::create([
                'catering_pkg_id' => $catering_package_category->id, 
                'image' => $path,
              ]);
          }
        }
        $data['catering_pkg_cat_id']   =  $request->category_name;
        $catering_package_category = $catering_package_category->update($data);
        if('catering_package_category'){
          DB::commit();
            return redirect()->route('catering_packages.index')->with('success',trans('catering_packages.updated'));
        } else {
            return redirect()->route('catering_packages.index')->with('error',trans('catering_packages.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $catering_package_category = CateringPackage::find($id);
        if(!$catering_package_category){
          return redirect()->route('catering_packages.index')->with('error',trans('common.no_data'));
        }
        if($catering_package_category->delete()){
            return redirect()->route('catering_packages.index')->with('success',trans('catering_packages.deleted'));
        }else{
            return redirect()->route('catering_packages.index')->with('error',trans('catering_packages.error'));
        }
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request) {
        $catering_package_category= CateringPackage::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($catering_package_category) {
        return response()->json(['success' => trans('catering_packages.status_updated')]);
       } else {
        return response()->json(['error' => trans('catering_packages.error')]);
       }
    }

     public function remove_media(Request $request)
    {
      $catering_package_media = CateringPackageMedia::where('id',$request->media_id)->first();
      $image= CateringPackageMedia::where('catering_pkg_id',$request->catering_pkg_id)->count();
              if($image == 1){
                return response()->json(['error' => trans('catering_packages.atleast_needed')]);
              }else{
                $messge = trans('catering_packages.image_deleted');
              }
            if(file_exists($catering_package_media->image)){
              unlink($catering_package_media->image);
            }       
            if($catering_package_media->delete()) {
              return response()->json(['success' => $messge]);
            }else {
              return response()->json(['error' => trans('medical_room.error')]);
            }
    }

}
   