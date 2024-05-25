<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:enquiry-list', ['only' => ['index','show']]);

    }

    public function index() {
        $enquirys = enquiry::all();
        return view('admin.enquiry.index',compact('enquirys'));
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



        $query = new Enquiry();   
    
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter = $query;

        if($searchValue != ''){
        $filter = $filter->where(function($q)use ($searchValue) {
                            $q->where('full_name','like','%'.$searchValue.'%')
                            ->orWhere('phone_no','like','%'.$searchValue.'%')
                            ->orWhere('email_id','like','%'.$searchValue.'%')
                            ->orWhere('subject','like','%'.$searchValue.'%')
                            ->orWhere('message','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%')
                            ->orWhere('id','like','%'.$searchValue.'%');
                     });
        }

        $filter_count = $filter->count();
        $totalRecordwithFilter = $filter_count;

        ## Fetch records
        $empQuery = $filter;
        $empQuery = $empQuery->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();
        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['show']= route("enquiry.show",$emp["id"]);
            $emp['delete'] = route("enquiry.destroy",$emp["id"]);
            
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
           $page_title = trans('enquiry.show'); 
        $enquiry = Enquiry::find($id);
        if($enquiry){
            return view('admin.enquiry.show',compact(['enquiry','page_title']));
        }else{
            return redirect()->route('enquiry.index')->with('error', trans('enquiry.somethink_wrong'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $enquiry = Enquiry::find($id);

        if(empty($enquiry) && $enquiry->count() == 0){
            return redirect()->route('enquiry.index')->with('error', trans('enquiry.no_enquiry_found'));
        }

        if($enquiry->delete()){
            return redirect()->route('enquiry.index')->with('success', trans('enquiry.enquiry_deleted_successfully'));
        }else{
            return redirect()->route('enquiry.index')->with('error', trans('enquiry.enquiry_deleted_unsuccessfully'));
        }
    }

    public function status(Request $request) {
        $enquiry= enquiry::where('id',$request->id)
               ->update(['status'=>$request->status]);
    
       if($enquiry){
        return response()->json(['success' => trans('enquiry.county_status_updated_succesfully')]);
       }else{
        return response()->json(['error' => trans('enquiry.county_status_updated_unsuccesfully')]);
       }
    }
}

