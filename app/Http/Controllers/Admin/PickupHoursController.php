<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PickupHour;
use App\Models\BusinessWorkingHour;
use DB;


class PickupHoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:pickup-hours-list', ['only' => ['index','show']]);
        $this->middleware('permission:pickup-hours-create', ['only' => ['create','store']]);
        $this->middleware('permission:pickup-hours-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:pickup-hours-status', ['only' => ['status']]);
        $this->middleware('permission:pickup-hours-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $business = \Auth::user()->business;
        if(!$business) {
          return redirect()->route('businesses.create')->with('success',trans('business_branches.business_details_required'));
        }
        
        $page_title = trans('pickup_hours.heading');
        $pickup_hours = PickupHour::where('business_id',\Auth::user()->business->id)->get();
        return view('admin.pickup_hours.index',compact('pickup_hours','page_title'));
    }

    public function index_ajax(Request $request){
        $query = PickupHour::query()->where('business_id',\Auth::user()->business->id);
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

            $filter = $filter->where(function($q)use ($searchValue) {
                          $q->where('from_time','like','%'.$searchValue.'%')
                          ->orWhere('to_time', 'like','%'.$searchValue.'%')
                          ->orWhereHas('translation',function($que) use ($searchValue){
                              $que->where('shift_name','like','%'.$searchValue.'%');
                          });                                 
                        });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'pickup_hour'){
            $filter = $filter->join('pickup_hour_translations', 'pickup_hour_translations.pickup_hour_id', '=', 'pickup_hours.id')
            ->orderBy('pickup_hour_translations.'.$columnName, $columnSortOrder)
            ->where('pickup_hour_translations.locale',\App::getLocale())
            ->select(['pickup_hours.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("pickup_hours.edit",$emp["id"]);
            $emp['show'] = route("pickup_hours.show",$emp["id"]);
            $emp['delete'] = route("pickup_hours.destroy",$emp["id"]);
            $emp['from_time'] = date("h:i A",strtotime($emp['from_time']));
            $emp['to_time'] = date("h:i A",strtotime($emp['to_time']));
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

        $business = \Auth::user()->business;
        if(!$business) {
          return redirect()->route('businesses.create')->with('success',trans('business_branches.business_details_required'));
        }

        $page_title = trans('pickup_hours.add_new');
  
        return view('admin.pickup_hours.create',compact('page_title'));
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
            'shift_name:en'        => 'required|min:3|max:100',
            'shift_name:ar'        => 'required|min:3|max:100',
            'from_time'            => 'required|date_format:h:i A',
            'to_time'              => 'required|date_format:h:i A|after:from_time',
            'pickup_slot_duration' => 'required|numeric',
            'status'               => 'required',
        ]);

        $data = $request->all();
        
        DB::beginTransaction();
        try {           
            $data['from_time'] = date("H:i",strtotime($data['from_time']));
            $data['to_time'] = date("H:i",strtotime($data['to_time']));
            $data['business_id'] = \Auth::user()->business->id;

            // validate if shift is within business working hour
            $working_hours = BusinessWorkingHour::where('business_id',$data['business_id'])->first();
            if(strtotime($data['from_time']) < strtotime($working_hours->from_time) || strtotime($data['to_time']) > strtotime($working_hours->to_time) )
            {
              return redirect()->back()->withInput()->with('error',trans('pickup_hours.start_and_end_time_should_be_include_in_business_working_hour'));
            }

            //validate if shift is not overlaping with other shift
            $existing_pkp_hours = PickupHour::where('business_id',$data['business_id'])->get();
            foreach ($existing_pkp_hours as $pkp_hr) {
              if((strtotime($data['from_time']) >= strtotime($pkp_hr->from_time) && strtotime($data['from_time']) < strtotime($pkp_hr->to_time)) || (strtotime($data['to_time']) >= strtotime($pkp_hr->from_time) && strtotime($data['to_time']) < strtotime($pkp_hr->to_time)) )
              {
                return redirect()->back()->withInput()->with('error',trans('pickup_hours.overlaping_with_other_shift'));
              }
            }

            $pickup_hour = PickupHour::create($data);
            if($pickup_hour) {
                DB::commit();
                return redirect()->route('pickup_hours.index')->with('success',trans('pickup_hours.added'));
            } else {
                return redirect()->route('pickup_hours.index')->with('error',trans('pickup_hours.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('pickup_hours.already_exists'));
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
        $page_title = trans('pickup_hours.show');
        $pickup_hour = PickupHour::find($id);
        if(!$pickup_hour) {
          return redirect()->route('pickup_hours.index')->with('error',trans('common.no_data'));
        }
        return view('admin.pickup_hours.show',compact('pickup_hour','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('pickup_hours.update');
        $pickup_hour = PickupHour::find($id);
        if(!$pickup_hour) {
          return redirect()->route('pickup_hours.index')->with('error',trans('common.no_data'));
        }
        return view('admin.pickup_hours.edit',compact('pickup_hour','page_title'));
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
            'shift_name:en' => 'required|min:3|max:100',
            'shift_name:ar' => 'required|min:3|max:100',
            'from_time' => 'required|date_format:h:i A',
            'to_time' => 'required|date_format:h:i A|after:from_time',
            'status' => 'required',
        ]);

        $data = $request->all();
        $data['from_time'] = date("H:i",strtotime($data['from_time']));
        $data['to_time'] = date("H:i",strtotime($data['to_time']));

        // validate if shift is within business working hour
        $working_hours = BusinessWorkingHour::where('business_id',\Auth::user()->business->id)->first();
        if(strtotime($data['from_time']) < strtotime($working_hours->from_time) || strtotime($data['to_time']) > strtotime($working_hours->to_time) )
        {
          return redirect()->back()->withInput()->with('error',trans('pickup_hours.start_and_end_time_should_be_include_in_business_working_hour'));
        }

        //validate if shift is not overlaping with other shift
        $existing_pkp_hours = PickupHour::where('id','!=',$id)->where('business_id',\Auth::user()->business->id)->get();
        foreach ($existing_pkp_hours as $pkp_hr) {
          if((strtotime($data['from_time']) >= strtotime($pkp_hr->from_time) && strtotime($data['from_time']) < strtotime($pkp_hr->to_time)) || (strtotime($data['to_time']) >= strtotime($pkp_hr->from_time) && strtotime($data['to_time']) < strtotime($pkp_hr->to_time)) )
          {
            return redirect()->back()->withInput()->with('error',trans('pickup_hours.overlaping_with_other_shift'));
          }
        }  

        $pickup_hour = PickupHour::find($id);
        if(!$pickup_hour) {
          return redirect()->route('pickup_hours.index')->with('error',trans('common.no_data'));
        }

        try {
          if($pickup_hour->update($data)){
              return redirect()->route('pickup_hours.index')->with('success',trans('pickup_hours.updated'));
          } else {
              return redirect()->route('pickup_hours.index')->with('error',trans('pickup_hours.error'));
          }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('pickup_hours.already_exists'));
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
        $pickup_hour = PickupHour::find($id);
        if($pickup_hour->delete()){
            return redirect()->route('pickup_hours.index')->with('success',trans('pickup_hours.deleted'));
        }else{
            return redirect()->route('pickup_hours.index')->with('error',trans('pickup_hours.error'));
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
        $pickup_hour= PickupHour::where('id',$request->id)->first();
        $pickup_hour->update(['status'=>$request->status]);
       if($pickup_hour) {
        return response()->json(['success' => trans('pickup_hours.status_updated')]);
       } else {
        return response()->json(['error' => trans('pickup_hours.error')]);
       }
    }
}
   