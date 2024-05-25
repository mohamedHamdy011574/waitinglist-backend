<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Helpers\CommonHelpers;
use Carbon\Carbon;

use App\Jobs\SendAdvertisementNotificationJob;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{
  use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:advertisement-list', ['only' => ['index','show']]);
        $this->middleware('permission:advertisement-create', ['only' => ['create','store']]);
        $this->middleware('permission:advertisement-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:advertisement-status', ['only' => ['status']]);
        $this->middleware('permission:advertisement-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $page_title = trans('advertisements.heading');
        $advertisements = Advertisement::all();
        return view('admin.advertisements.index',compact('advertisements','page_title'));
    }

    public function index_ajax(Request $request){
        $query = Advertisement::query();
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
                            ->orWhere('duration_from','like','%'.$searchValue.'%')
                            ->orWhere('duration_to','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%');
                         });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'name'){
            $filter = $filter->join('advertisement_translations', 'advertisement_translations.advertisement_id', '=', 'advertisements.id')
            ->orderBy('advertisement_translations.'.$columnName, $columnSortOrder)
            ->where('advertisement_translations.locale',App::getLocale())
            ->select(['advertisements.*']);
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
                $service_include .= trans('advertisements.'.$s) .', '; 
            }
            $emp['edit'] = route("advertisements.edit",$emp["id"]);
            $emp['show'] = route("advertisements.show",$emp["id"]);
            $emp['delete'] = route("advertisements.destroy",$emp["id"]);
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
        $page_title = trans('advertisements.add_new');
  
        return view('admin.advertisements.create',compact('page_title'));
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
            'name:en' => 'required|min:3|max:100',
            'name:ar' => 'required|min:3|max:100',
            'video' => 'required|file|mimes:flv,mp4,m3u8,ts,3gp,mov,avi,wmv|max:10240',
            'duration_from' => 'required|date|date_format:Y-m-d|after:yesterday',
            'duration_to' => 'required|date|date_format:Y-m-d|after:duration_from',
            'status' => 'required',
        ]);


        $data = $request->all();
        $data['added_by'] = Auth::user()->id;

        if($data['video']) {
          $path = $this->saveMedia($request->video,'advertisements');
          $data['video'] = $path; 
        }

        // echo "<pre>"; print_r($data); 
        DB::beginTransaction();
        try {
            $advertisement = Advertisement::create($data);
            if($advertisement) {
                DB::commit();

              if($advertisement->duration_from == date('Y-m-d')) {
                //notifications
                SendAdvertisementNotificationJob::dispatch($advertisement)->delay(now()->addSeconds(5));
                /*$users = User::where('user_type','Customer')
                    ->where(['verified' => 1, 'status' => 'active'])
                    ->get();
                foreach ($users as $user) {
                  $title = trans('advertisements.notification_title');
                  $body = trans('advertisements.notification_body');
                  $nres = $this->sendNotification($user,$title,$body,"advertisement",$advertisement->id);
                  // echo '<pre>'; print_r($nres); die;
                }*/
                $advertisement->notified_at = Carbon::now();  
                $advertisement->save(); 
              }  

                return redirect()->route('advertisements.index')->with('success',trans('advertisements.added'));
            } else {
                return redirect()->route('advertisements.index')->with('error',trans('advertisements.error'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('advertisements.already_exists'));
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
        $page_title = trans('advertisements.show');
        $advertisement = Advertisement::find($id);
        if(!$advertisement) {
          return redirect()->route('advertisements.index')->with('error',trans('common.no_data'));
        }
        return view('admin.advertisements.show',compact('advertisement','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('advertisements.update');
        $advertisement = Advertisement::find($id);
        if(!$advertisement) {
          return redirect()->route('advertisements.index')->with('error',trans('common.no_data'));
        }
        return view('admin.advertisements.edit',compact('advertisement','page_title'));
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
            'name:en' => 'required|min:3|max:100',
            'name:ar' => 'required|min:3|max:100',
            'video' => 'file|mimes:flv,mp4,m3u8,ts,3gp,mov,avi,wmv|max:10240',
            'duration_from' => 'required|date|date_format:Y-m-d|after:yesterday',
            'duration_to' => 'required|date|date_format:Y-m-d|after:duration_from',
            'status' => 'required',
        ]);

        $data = $request->all();

        
        $advertisement = Advertisement::find($id);
        if(!$advertisement) {
          return redirect()->route('advertisements.index')->with('error',trans('common.no_data'));
        }

        try {

          //create menu_item_photo
          if($request->has('video')) {
                $path = $this->saveMedia($request->file('video'),'advertisements');
                $data['video'] = $path;
                unlink($advertisement->video);
          }

          if($advertisement->update($data)){
              return redirect()->route('advertisements.index')->with('success',trans('advertisements.updated'));
          } else {
              return redirect()->route('advertisements.index')->with('error',trans('advertisements.error'));
          }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withError(trans('advertisements.already_exists'));
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
        $advertisement = Advertisement::find($id);
        if($advertisement->delete()){
            return redirect()->route('advertisements.index')->with('success',trans('advertisements.deleted'));
        }else{
            return redirect()->route('advertisements.index')->with('error',trans('advertisements.error'));
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
        $advertisement= Advertisement::where('id',$request->id)->first();
        $advertisement->update(['status'=>$request->status]);
       if($advertisement) {
        return response()->json(['success' => trans('advertisements.status_updated')]);
       } else {
        return response()->json(['error' => trans('advertisements.error')]);
       }
    }
}
   