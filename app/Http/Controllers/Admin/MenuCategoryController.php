<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuCategory;
use DB, Auth;


class MenuCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:menu-category-list', ['only' => ['index','show']]);
        $this->middleware('permission:menu-category-create', ['only' => ['create','store']]);
        $this->middleware('permission:menu-category-edit', ['only' => ['edit','update']]);
    }
    public function index()
    {
        $page_title = trans('menu_categories.heading');
        $menu_categories = MenuCategory::all();
        return view('admin.menu_categories.index',compact('menu_categories','page_title'));
    }

    public function index_ajax(Request $request){
        $user = auth()->user();
        // $query = MenuCategory::query();
        $query = MenuCategory::where('business_id', $user->business->id);
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
                            ->where('name','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%');
                         });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'name'){
            $filter = $filter->join('menu_category_translations', 'menu_category_translations.menu_category_id', '=', 'menu_categories.id')
            ->orderBy('menu_category_translations.'.$columnName, $columnSortOrder)
            ->where('menu_category_translations.locale',\App::getLocale())
            ->select(['menu_categories.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("menu_categories.edit",$emp["id"]);
            $emp['show'] = route("menu_categories.show",$emp["id"]);
            $emp['delete'] = route("menu_categories.destroy",$emp["id"]);
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
        $page_title = trans('menu_categories.add_new');
  
        return view('admin.menu_categories.create',compact('page_title'));
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
            'name:ar' => 'required|max:255',
            'name:en' => 'required|max:255',
        ]);

        $data = $request->all();
        $data['business_id'] = Auth::user()->business->id;

        DB::beginTransaction();
        try {
            $menu_category = MenuCategory::create($data);
            // echo '<pre>'; print_r($data); die;
            if($menu_category) {
                DB::commit();
                return redirect()->route('menu_categories.index')->with('success',trans('menu_categories.added'));
            } else {
                return redirect()->route('menu_categories.index')->with('error',trans('menu_categories.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('menu_categories.already_exists'));
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
        $page_title = trans('menu_categories.show');
        $menu_category = MenuCategory::find($id);
        if(!$menu_category) {
          return redirect()->route('menu_categories.index')->with('error',trans('common.no_data'));
        }
        return view('admin.menu_categories.show',compact('menu_category','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('menu_categories.update');
        $menu_category = MenuCategory::find($id);
        if(!$menu_category) {
          return redirect()->route('menu_categories.index')->with('error',trans('common.no_data'));
        }
        return view('admin.menu_categories.edit',compact('menu_category','page_title'));
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
            'name:ar' => 'required|max:30',
            'name:en' => 'required|max:30',
        ]);

        $data = $request->all();
        $menu_category = MenuCategory::find($id);

        DB::beginTransaction();
        try {
            if($menu_category->update($data)){
                DB::commit();
                return redirect()->route('menu_categories.index')->with('success',trans('menu_categories.updated'));
            } else {
                return redirect()->route('menu_categories.index')->with('error',trans('menu_categories.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('menu_categories.already_exists'));
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
        $menu_category = MenuCategory::find($id);
        if($menu_category->delete()){
            return redirect()->route('menu_categories.index')->with('success',trans('menu_categories.deleted'));
        }else{
            return redirect()->route('menu_categories.index')->with('error',trans('menu_categories.error'));
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
        $menu_category= MenuCategory::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($menu_category) {
        return response()->json(['success' => trans('menu_categories.status_updated')]);
       } else {
        return response()->json(['error' => trans('menu_categories.error')]);
       }
    }
}
   