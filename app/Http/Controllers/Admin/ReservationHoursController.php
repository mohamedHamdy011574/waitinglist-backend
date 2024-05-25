<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReservationHour;
use App\Models\BusinessWorkingHour;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;


class ReservationHoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:reservation-hours-list', ['only' => ['index','show']]);
        $this->middleware('permission:reservation-hours-create', ['only' => ['create','store']]);
        $this->middleware('permission:reservation-hours-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:reservation-hours-status', ['only' => ['status']]);
        $this->middleware('permission:reservation-hours-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $business = Auth::user()->business;
        if(!$business) {
          return redirect()->route('businesses.create')->with('success',trans('business_branches.business_details_required'));
        }
        
        $page_title = trans('reservation_hours.heading');
        $reservation_hours = ReservationHour::where('business_id',Auth::user()->business->id)->get();
        return view('admin.reservation_hours.index',compact('reservation_hours','page_title'));
    }

    public function index_ajax(Request $request){
        $query = ReservationHour::query()->where('business_id',Auth::user()->business->id);
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
                          ->orWhere('allowed_chair', 'like','%'.$searchValue.'%')
                          ->orWhereHas('translation',function($que) use ($searchValue){
                              $que->where('shift_name','like','%'.$searchValue.'%');
                          });                                 
                        });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        $filter = ($columnName == 'reservation_hour') ? $filter->join('reservation_hour_translations', 'reservation_hour_translations.reservation_hour_id', '=', 'reservation_hours.id')
            ->orderBy('reservation_hour_translations.'.$columnName, $columnSortOrder)
            ->where('reservation_hour_translations.locale',App::getLocale())
            ->select(['reservation_hours.*']) : $filter->orderBy($columnName, $columnSortOrder);
        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['edit'] = route("reservation_hours.edit",$emp["id"]);
            $emp['show'] = route("reservation_hours.show",$emp["id"]);
            $emp['delete'] = route("reservation_hours.destroy",$emp["id"]);
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

        $business = Auth::user()->business;
        if(!$business) {
          return redirect()->route('businesses.create')->with('success',trans('business_branches.business_details_required'));
        }

        $page_title = trans('reservation_hours.add_new');
  
        return view('admin.reservation_hours.create',compact('page_title'));
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
            'allowed_chair'        => 'required|numeric',
            'dining_slot_duration' => 'required|numeric',
            'status'               => 'required',
        ]);

        $data = $request->all();
        
        DB::beginTransaction();
        try {           
            $data['from_time'] = date("H:i",strtotime($data['from_time']));
            $data['to_time'] = date("H:i",strtotime($data['to_time']));
            $data['business_id'] = Auth::user()->business->id;

            // validate if shift is within business working hour
            $working_hours = BusinessWorkingHour::where('business_id',$data['business_id'])->first();
            if(strtotime($data['from_time']) < strtotime($working_hours->from_time) || strtotime($data['to_time']) > strtotime($working_hours->to_time) )
            {
              return redirect()->back()->withInput()->with('error',trans('reservation_hours.start_and_end_time_should_be_include_in_business_working_hour'));
            }

            //validate if shift is not overlaping with other shift
            $existing_res_hours = ReservationHour::where('business_id',$data['business_id'])->get();
            foreach ($existing_res_hours as $res_hr) {
              if((strtotime($data['from_time']) >= strtotime($res_hr->from_time) && strtotime($data['from_time']) < strtotime($res_hr->to_time)) || (strtotime($data['to_time']) >= strtotime($res_hr->from_time) && strtotime($data['to_time']) < strtotime($res_hr->to_time)) )
              {
                return redirect()->back()->withInput()->with('error',trans('reservation_hours.overlaping_with_other_shift'));
              }
            }

            $reservation_hour = ReservationHour::create($data);
            if($reservation_hour) {
                DB::commit();
                return redirect()->route('reservation_hours.index')->with('success',trans('reservation_hours.added'));
            } else {
                return redirect()->route('reservation_hours.index')->with('error',trans('reservation_hours.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('reservation_hours.already_exists'));
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
        $page_title = trans('reservation_hours.show');
        $reservation_hour = ReservationHour::find($id);
        if(!$reservation_hour) {
          return redirect()->route('reservation_hours.index')->with('error',trans('common.no_data'));
        }
        return view('admin.reservation_hours.show',compact('reservation_hour','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('reservation_hours.update');
        $reservation_hour = ReservationHour::find($id);
        if(!$reservation_hour) {
          return redirect()->route('reservation_hours.index')->with('error',trans('common.no_data'));
        }
        return view('admin.reservation_hours.edit',compact('reservation_hour','page_title'));
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
            'allowed_chair' => 'required|numeric',
            'status' => 'required',
        ]);

        $data = $request->all();
        $data['from_time'] = date("H:i",strtotime($data['from_time']));
        $data['to_time'] = date("H:i",strtotime($data['to_time']));


        // validate if shift is within business working hour
        $working_hours = BusinessWorkingHour::where('business_id',Auth::user()->business->id)->first();
        if(strtotime($data['from_time']) < strtotime($working_hours->from_time) || strtotime($data['to_time']) > strtotime($working_hours->to_time) )
        {
          return redirect()->back()->withInput()->with('error',trans('reservation_hours.start_and_end_time_should_be_include_in_business_working_hour'));
        }

        //validate if shift is not overlaping with other shift
        $existing_res_hours = ReservationHour::where('id','!=',$id)->where('business_id',Auth::user()->business->id)->get();
        foreach ($existing_res_hours as $res_hr) {
          if((strtotime($data['from_time']) >= strtotime($res_hr->from_time) && strtotime($data['from_time']) < strtotime($res_hr->to_time)) || (strtotime($data['to_time']) >= strtotime($res_hr->from_time) && strtotime($data['to_time']) < strtotime($res_hr->to_time)) )
          {
            return redirect()->back()->withInput()->with('error',trans('reservation_hours.overlaping_with_other_shift'));
          }
        } 

        $reservation_hour = ReservationHour::find($id);
        if(!$reservation_hour) {
          return redirect()->route('reservation_hours.index')->with('error',trans('common.no_data'));
        }

        try {
          if($reservation_hour->update($data)){
              return redirect()->route('reservation_hours.index')->with('success',trans('reservation_hours.updated'));
          } else {
              return redirect()->route('reservation_hours.index')->with('error',trans('reservation_hours.error'));
          }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('reservation_hours.already_exists'));
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
        $reservation_hour = ReservationHour::find($id);
        if($reservation_hour->delete()){
            return redirect()->route('reservation_hours.index')->with('success',trans('reservation_hours.deleted'));
        }else{
            return redirect()->route('reservation_hours.index')->with('error',trans('reservation_hours.error'));
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
        $reservation_hour= ReservationHour::where('id',$request->id)->first();
        $reservation_hour->update(['status'=>$request->status]);
       if($reservation_hour) {
        return response()->json(['success' => trans('reservation_hours.status_updated')]);
       } else {
        return response()->json(['error' => trans('reservation_hours.error')]);
       }
    }
}
   