<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Concern;
use App\Models\Report;
use DB;


class ConcernController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:concern-list', ['only' => ['index','show']]);
        $this->middleware('permission:concern-create', ['only' => ['create','store']]);
        $this->middleware('permission:concern-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:concern-status', ['only' => ['status']]);
        $this->middleware('permission:concern-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $page_title = trans('concerns.heading');
        $concerns = Concern::all();
        return view('admin.concerns.index',compact('concerns','page_title'));
    }

    public function index_ajax(Request $request){
        $query = Concern::query();
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
                            ->where('concern','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%');
                         });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'concern'){
            $filter = $filter->join('concern_translations', 'concern_translations.concern_id', '=', 'concerns.id')
            ->orderBy('concern_translations.'.$columnName, $columnSortOrder)
            ->where('concern_translations.locale',\App::getLocale())
            ->select(['concerns.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("concerns.edit",$emp["id"]);
            $emp['show'] = route("concerns.show",$emp["id"]);
            $emp['delete'] = route("concerns.destroy",$emp["id"]);
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
        $page_title = trans('concerns.add_new');
  
        return view('admin.concerns.create',compact('page_title'));
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
            'concern:en' => 'required|min:3|max:100',
            'concern:ar' => 'required|min:3|max:100',
            'status' => 'required',
        ]);

        $data = $request->all();
        
        DB::beginTransaction();
        try {           

            $concern = Concern::create($data);
            if($concern) {
                DB::commit();
                return redirect()->route('concerns.index')->with('success',trans('concerns.added'));
            } else {
                return redirect()->route('concerns.index')->with('error',trans('concerns.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('concerns.already_exists'));
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
        $page_title = trans('concerns.show');
        $concern = Concern::find($id);
        if(!$concern) {
          return redirect()->route('concerns.index')->with('error',trans('common.no_data'));
        }
        return view('admin.concerns.show',compact('concern','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('concerns.update');
        $concern = Concern::find($id);
        if(!$concern) {
          return redirect()->route('concerns.index')->with('error',trans('common.no_data'));
        }
        return view('admin.concerns.edit',compact('concern','page_title'));
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
            'concern:en' => 'required|min:3|max:100',
            'concern:ar' => 'required|min:3|max:100',
            'status' => 'required',
        ]);

        $data = $request->all();

        $concern = Concern::find($id);
        if(!$concern) {
          return redirect()->route('concerns.index')->with('error',trans('common.no_data'));
        }

        try {
          if($concern->update($data)){
              return redirect()->route('concerns.index')->with('success',trans('concerns.updated'));
          } else {
              return redirect()->route('concerns.index')->with('error',trans('concerns.error'));
          }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('concerns.already_exists'));
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
        $concern = Concern::find($id);
        if($concern->delete()){
            return redirect()->route('concerns.index')->with('success',trans('concerns.deleted'));
        }else{
            return redirect()->route('concerns.index')->with('error',trans('concerns.error'));
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
        $concern= Concern::where('id',$request->id)->first();
        if(Report::where('concern_id',$request->id)->get()->count()){
          return response()->json(['error' => trans('concerns.cant_inactive')]);
        }
        $concern->update(['status'=>$request->status]);
       if($concern) {
        return response()->json(['success' => trans('concerns.status_updated')]);
       } else {
        return response()->json(['error' => trans('concerns.error')]);
       }
    }
}
   