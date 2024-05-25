<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Carbon\Carbon;
use App\Models\Helpers\CommonHelpers;

class NewsController extends Controller
{
    use CommonHelpers;

    public function __construct(){
        $this->middleware('auth');

        $this->middleware('permission:news-list', ['only' => ['index','show']]);
        $this->middleware('permission:news-create', ['only' => ['create','store']]);
        $this->middleware('permission:news-edit', ['only' => ['edit','update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = trans('news.heading'); 
        return view ('admin.news.index',compact('page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function index_ajax(Request $request)
    {
        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        $query = new News();   
    
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter = $query;
        if($searchValue != '') {
        $filter = $filter->where(function($q)use ($searchValue) {
                            $q->whereHas('translation',function($query) use ($searchValue){
                                 $query->where('headline','like','%'.$searchValue.'%')
                                        ->orWhere('description','like','%'.$searchValue.'%')
                                        ->orWhere('id','like','%'.$searchValue.'%') 
                                        ->orWhere('created_at','like','%'.$searchValue.'%');
                                        
                                        })
                            ->orWhere('status','like','%'.$searchValue.'%');
                            // ->orWhere('banner','like','%'.$searchValue.'%');
                     });
        }


        $filter_count = $filter->count();
        $totalRecordwithFilter = $filter_count;

        if($columnName == 'headline' || $columnName == 'description'){
            $filter = $filter->join('news_translations', 'news_translations.news_id', '=', 'news.id')
            ->orderBy('news_translations.'.$columnName, $columnSortOrder)
            ->where('news_translations.locale',\App::getLocale())
            ->select(['news.*']);
        }else{
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }

        ## Fetch records
        $empQuery = $filter;
        $empQuery = $empQuery->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();
        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons   

       $emp['banner'] = '<img src="'.asset(News::where('id',$emp->id)->value('banner')).'" style="width:50px">';

            $emp['edit']= route("news.edit",$emp["id"]);
            $emp['show']= route("news.show",$emp["id"]);
            $date = date($emp->created_at->todatestring());
            $emp['date'] = date("d-M-Y", strtotime($date));

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

    public function create()
    {
        $page_title = trans('news.add_new');        
         return view('admin.news.create',compact('page_title'));
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
            'headline:ar'       =>     'required|max:255',
            'headline:en'       =>     'required|max:255',
            'description:ar'    =>     'required',
            'description:en'    =>     'required',
            'banner'            =>     'required|image|mimes:png,jpg,jpeg|max:10000',
        ]);
    try{
        $data = $request->all();    
        // echo '<pre>'; print_r($data); die;
        if($data['banner']) {
         $path = $this->saveMedia($request->banner,'news_banner');
         $data['banner'] = $path; 
        }
        $news = News::create($data);
        if($news) {
            return redirect()->route('news.index')->with('success',trans('news.added'));
        } else {
            return redirect()->route('news.index')->with('error',trans('common.something_went_wrong'));
        }
    }  catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
    try {
         $page_title = trans('common.show');
        $news= News::find($id);
        if(!$news) {
          return redirect()->route('news.index')->with('error',trans('common.no_data'));
        }
        return view('admin.news.show',compact('news','page_title'));
    }catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
    try{
            $page_title = trans('news.update');
            $news = News::find($id);
            if(!$news) {
              return redirect()->route('news.index')->with('error',trans('common.no_data'));
            } 
            return view('admin.news.edit',compact('news','page_title'));
    } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
    }
}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
     $validator= $request->validate([
            'headline:ar'       =>     'required|max:255',
            'headline:en'       =>     'required|max:255',
            'description:ar'    =>     'required',
            'description:en'    =>     'required',
            'banner'            =>     'sometimes|required|image|mimes:png,jpg,jpeg|max:10000',
        ]);

    try{
        $data = $request->all();
        $news = News::find($id);

        if($request->has('banner')) {
            unlink($news->banner);
            $path = $this->saveMedia($request->banner,'news_banner');
            $data['banner'] = $path; 
        }
        if(empty($news)){
            return redirect()->route('news.index')->with('error',trans('news.something_went_wrong'));
        }

        if($news->update($data)){
            return redirect()->route('news.index')->with('success',trans('news.updated'));  
        } else {
            return redirect()->route('news.index')->with('error',trans('common.something_went_wrong'));
        }
      } catch (Exception $e) {
        return redirect()->back()->with('error',$e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      public function status(Request $request)
      {
        $news= News::where('id',$request->id)
               ->update(['status'=>$request->status]);
       if($news){
        return response()->json(['success' => trans('news.news_status_update_successfully')]);
       }else{
        return response()->json(['error' => trans('news.news_status_update_unsuccesfully')]);
       }   
    }
}
