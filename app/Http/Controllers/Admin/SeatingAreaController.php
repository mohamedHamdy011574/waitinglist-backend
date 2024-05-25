<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeatingArea;
use DB;


class SeatingAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:seating-area-list', ['only' => ['index','show']]);
        $this->middleware('permission:seating-area-create', ['only' => ['create','store']]);
        $this->middleware('permission:seating-area-edit', ['only' => ['edit','update']]);
    }
    public function index()
    {
        $page_title = trans('seating_area.heading');
        $restaturant_features = SeatingArea::all();
        return view('admin.seating_area.index',compact('restaturant_features','page_title'));
    }

    public function index_ajax(Request $request){
        $query = SeatingArea::query();
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
            $filter = $filter->join('seating_area_translations', 'seating_area_translations.stg_area_id', '=', 'seating_area.id')
            ->orderBy('seating_area_translations.'.$columnName, $columnSortOrder)
            ->where('seating_area_translations.locale',\App::getLocale())
            ->select(['seating_area.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("seating_area.edit",$emp["id"]);
            $emp['show'] = route("seating_area.show",$emp["id"]);
            $emp['delete'] = route("seating_area.destroy",$emp["id"]);
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
        $page_title = trans('seating_area.add_new');
  
        return view('admin.seating_area.create',compact('page_title'));
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
            if(SeatingArea::create($data)) {
                DB::commit();
                return redirect()->route('seating_area.index')->with('success',trans('seating_area.added'));
            } else {
                return redirect()->route('seating_area.index')->with('error',trans('seating_area.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('seating_area.already_exists'));
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
        $page_title = trans('seating_area.show');
        $seating_area  = SeatingArea::find($id);
        if(!$seating_area) {
          return redirect()->route('seating_area.index')->with('error',trans('common.no_data'));
        }
        return view('admin.seating_area.show',compact('seating_area','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('seating_area.update');
        $seating_area  = SeatingArea::find($id);
        if(!$seating_area) {
          return redirect()->route('seating_area.index')->with('error',trans('common.no_data'));
        }
        return view('admin.seating_area.edit',compact('seating_area','page_title'));
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
        $seating_area  = SeatingArea::find($id);

        DB::beginTransaction();
        try {
            if($seating_area ->update($data)){
                DB::commit();
                return redirect()->route('seating_area.index')->with('success',trans('seating_area.updated'));
            } else {
                return redirect()->route('seating_area.index')->with('error',trans('seating_area.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('seating_area.already_exists'));
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
        $seating_area  = SeatingArea::find($id);
        if($seating_area ->delete()){
            return redirect()->route('seating_area.index')->with('success',trans('seating_area.deleted'));
        }else{
            return redirect()->route('seating_area.index')->with('error',trans('seating_area.error'));
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
        $seating_area = SeatingArea::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($seating_area ) {
        return response()->json(['success' => trans('seating_area.status_updated')]);
       } else {
        return response()->json(['error' => trans('seating_area.error')]);
       }
    }
}
   