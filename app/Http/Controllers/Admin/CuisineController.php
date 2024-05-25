<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cuisine;
use DB;


class CuisineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:cuisine-list', ['only' => ['index','show']]);
        $this->middleware('permission:cuisine-create', ['only' => ['create','store']]);
        $this->middleware('permission:cuisine-edit', ['only' => ['edit','update']]);
    }
    public function index()
    {
        $page_title = trans('cuisines.heading');
        $cuisines = Cuisine::all();
        return view('admin.cuisines.index',compact('cuisines','page_title'));
    }

    public function index_ajax(Request $request){
        $query = Cuisine::query();
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
                            ->where('description','like','%'.$searchValue.'%')
                            ->orWhere('name','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%');
                         });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'name' || $columnName == 'description'){
            $filter = $filter->join('cuisine_translations', 'cuisine_translations.cuisine_id', '=', 'cuisines.id')
            ->orderBy('cuisine_translations.'.$columnName, $columnSortOrder)
            ->where('cuisine_translations.locale',\App::getLocale())
            ->select(['cuisines.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("cuisines.edit",$emp["id"]);
            $emp['show'] = route("cuisines.show",$emp["id"]);
            $emp['delete'] = route("cuisines.destroy",$emp["id"]);
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
        $page_title = trans('cuisines.add_new');
  
        return view('admin.cuisines.create',compact('page_title'));
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
        DB::beginTransaction();
        try {
            // echo '<pre>'; print_r($data); die;
            if(Cuisine::create($data)) {
                DB::commit();
                return redirect()->route('cuisines.index')->with('success',trans('cuisines.added'));
            } else {
                return redirect()->route('cuisines.index')->with('error',trans('cuisines.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('cuisines.already_exists'));
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
        $page_title = trans('cuisines.show');
        $cuisine = Cuisine::find($id);
        if(!$cuisine) {
          return redirect()->route('cuisines.index')->with('error',trans('common.no_data'));
        }
        return view('admin.cuisines.show',compact('cuisine','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('cuisines.update');
        $cuisine = Cuisine::find($id);
        if(!$cuisine) {
          return redirect()->route('cuisines.index')->with('error',trans('common.no_data'));
        }
        return view('admin.cuisines.edit',compact('cuisine','page_title'));
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
        $cuisine = Cuisine::find($id);
        DB::beginTransaction();
        try {
            if($cuisine->update($data)){
                DB::commit();
                return redirect()->route('cuisines.index')->with('success',trans('cuisines.updated'));
            } else {
                return redirect()->route('cuisines.index')->with('error',trans('cuisines.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('cuisines.already_exists'));
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
        $cuisine = Cuisine::find($id);
        if($cuisine->delete()){
            return redirect()->route('cuisines.index')->with('success',trans('cuisines.deleted'));
        }else{
            return redirect()->route('cuisines.index')->with('error',trans('cuisines.error'));
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
        $cuisine= Cuisine::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($cuisine) {
        return response()->json(['success' => trans('cuisines.status_updated')]);
       } else {
        return response()->json(['error' => trans('cuisines.error')]);
       }
    }
}
   