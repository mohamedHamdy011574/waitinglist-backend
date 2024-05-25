<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\Subscription;


class SponsorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:sponsor-list', ['only' => ['index','show']]);
        $this->middleware('permission:sponsor-list-status', ['only' => ['status']]);
    }

    public function index()
    {
        $page_title = trans('sponsors.heading');
        $packages = Sponsor::all();
        return view('admin.sponsors.index',compact('packages','page_title'));
    }

    public function index_ajax(Request $request){
        $query = Sponsor::query();
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
                            ->where('package_for','like','%'.$searchValue.'%')
                            ->orWhere('package_name','like','%'.$searchValue.'%')
                            ->orWhere('description','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%');
                         });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'package_name' || $columnName == 'description'){
            $filter = $filter->join('package_translations', 'package_translations.package_id', '=', 'packages.id')
            ->orderBy('package_translations.'.$columnName, $columnSortOrder)
            ->where('package_translations.locale',\App::getLocale())
            ->select(['packages.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $service_include_array = explode(',', $emp['service_include']);
            $service_include = '';
            foreach ($service_include_array as $s) {
                $service_include .= trans('sponsors.'.$s) .', ';    
            }
            $emp['service_include'] = rtrim(trim($service_include),',');
            $emp['package_for'] = trans('sponsors.'.$emp['package_for']);
            $emp['edit'] = route("sponsors.edit",$emp["id"]);
            $emp['show'] = route("sponsors.show",$emp["id"]);
            $emp['delete'] = route("sponsors.destroy",$emp["id"]);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = trans('sponsors.show');
        $sponsor = Sponsor::find($id);
        if(!$sponsor) {
          return redirect()->route('sponsors.index')->with('error',trans('common.no_data'));
        }
        return view('admin.sponsors.show',compact('sponsor','page_title'));
    }


    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request)
    {
      $sponsor= Sponsor::where('id',$request->id)->first();
      if($sponsor) {
        $sponsor->update(['status'=>$request->status]);
        return response()->json(['success' => trans('sponsors.status_updated')]);
      } else {
        return response()->json(['error' => trans('sponsors.error')]);
      }
    }
}
   