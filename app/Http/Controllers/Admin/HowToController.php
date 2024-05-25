<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HowTo;
use App\Models\Translations\HowToTranslation;


class HowToController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:cms-list', ['only' => ['index','show']]);
        $this->middleware('permission:cms-create', ['only' => ['create','store']]);
        $this->middleware('permission:cms-edit', ['only' => ['edit','update']]);
    }
    public function index()
    {
        $page_title = trans('how_to.heading');
        $how_to = HowTo::all();
        return view('admin.how_to.index',compact('how_to','page_title'));
    
    }

    public function index_ajax(Request $request){
        // print_r($request);

        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        $query = HowTo::query();
    
        ## Total number of records without filtering
        $totalRecords = $query->count();

        ## Total number of record with filtering
        $filter= $query;
        if($searchValue != ''){
            $filter =   $filter->whereHas('translation',function($query) use ($searchValue) {
                          $query->where('display_order','like','%'.$searchValue.'%')
                          ->orWhere('question','like','%'.$searchValue.'%')
                          ->orWhere('answer','like','%'.$searchValue.'%');
                         });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'question' || $columnName == 'answer'){
            $filter = $filter->join('how_to_translations', 'how_to_translations.how_to_id', '=', 'how_to.id')
            ->orderBy('how_to_translations.'.$columnName, $columnSortOrder)
            ->where('how_to_translations.locale',\App::getLocale())
            ->select(['how_to.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }

        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("how_to.edit",$emp["id"]);
            $emp['show'] = route("how_to.show",$emp["id"]);
            $emp['delete'] = route("how_to.destroy",$emp["id"]);
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

    public function create() {
        $page_title = trans('how_to.add_new');
        return view('admin.how_to.create',compact('page_title'));
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
            'question:ar'  => 'required|min:3|max:150',
            'question:en'  => 'required|min:3|max:150',
            'answer:ar'    => 'required|min:3',
            'answer:en'    => 'required|min:3',
            'display_order' => 'required|integer',
        ]);
        $data = $request->all();
        // echo '<pre>'; print_r($data); die;
        if(HowTo::create($data)) {
            return redirect()->route('how_to.index')->with('success',trans('how_to.how_to_saved_successfully'));
        } else {
            return redirect()->route('how_to.index')->with('error',trans('how_to.how_to_saved_unsuccessfully'));
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
        $page_title = trans('how_to.show');
        $how_to = HowTo::find($id);
        if(!$how_to) {
          return redirect()->route('how_to.index')->with('error',trans('common.no_data'));
        }
        return view('admin.how_to.show',compact('how_to','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('how_to.update');
        $how_to = HowTo::find($id);
        if(!$how_to) {
          return redirect()->route('how_to.index')->with('error',trans('common.no_data'));
        }
        return view('admin.how_to.edit',compact('how_to','page_title'));
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

        $data = $request->all();
        $how_to = HowTo::find($id);
        
        if(empty($how_to)){
            return redirect()->route('how_to.index')->with('error',trans('how_to.something_went_wrong'));
        }

        $validator= $request->validate([
            'question:ar'  => 'required|min:3|max:150',
            'question:en'  => 'required|min:3|max:150',
            'answer:ar'    => 'required|min:3',
            'answer:en'    => 'required|min:3',
            'display_order' => 'required|integer',
        ]);
        

        if($how_to->update($data)){
            return redirect()->route('how_to.index')->with('success',trans('how_to.how_to_update_success'));
        } else {
            return redirect()->route('how_to.index')->with('error',trans('how_to.how_to_update_unsuccess'));
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
        $how_to = HowTo::find($id);

        if($how_to->delete()){
            return redirect()->route('how_to.index')->with('success',trans('how_to.how_to_deleted_Successfully'));
        }else{
            return redirect()->route('how_to.index')->with('error',trans('how_to.how_to_deleted_UnSuccessfully'));
        }
    }

    public function status(Request $request)
    {
        $how_to= HowTo::where('id',$request->id)
               ->update(['status'=>$request->status]);
    
       if($how_to){
        return response()->json(['success' => trans('how_to.how_to_status_update_sucess')]);
       }else{
        return response()->json(['error' => trans('how_to.how_to_status_update_unsucess')]);
       }
    }
}
   