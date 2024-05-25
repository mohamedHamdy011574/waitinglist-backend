<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Report;
use DB;


class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:review-list', ['only' => ['index','show']]);
        $this->middleware('permission:review-status', ['only' => ['status']]);
    }
    public function index()
    {
        $page_title = trans('reviews.heading');
        $reviews = Review::all();
        return view('admin.reviews.index',compact('reviews','page_title'));
    }

    public function index_ajax(Request $request){
        DB::enableQueryLog();
        $query = Review::query();
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
        $filter= $query->hasReports();
        if($searchValue != ''){
            $filter = $filter->where(function($q)use ($searchValue) {
                            $q->where('review','like','%'.$searchValue.'%')
                            ->orWhere('status', 'like','%'.$searchValue.'%')
                            ->orWhereHas('food_blog',function($que) use ($searchValue){
                                $que->where('recipe_name','like','%'.$searchValue.'%');
                            })
                            ->orWhereHas('customer',function($que) use ($searchValue){
                                $que->where('first_name','like','%'.$searchValue.'%');
                                $que->orWhere('last_name','like','%'.$searchValue.'%');
                            });                                   
                          });

        }

        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        if($columnName == 'recipe_name'){
            $filter = $filter->join('food_blogs', 'food_blogs.id', '=', 'reviews.blog_id')
            ->orderBy('food_blogs.'.$columnName, $columnSortOrder)
            ->select(['reviews.*']);
        } elseif($columnName == 'added_by'){
            $filter = $filter->join('users', 'users.id', '=', 'reviews.given_by')
            ->orderBy('users.first_name', $columnSortOrder)
            ->select(['reviews.*']);
        } elseif($columnName == 'report_count'){
            $filter = $filter->withCount('reports')->orderBy('reports_count', $columnSortOrder);
        } else{
            $filter = $filter->select(['reviews.*']);
            $filter = $filter->orderBy($columnName, $columnSortOrder);
        }
        ## Fetch records
        $empQuery = $filter->offset($row)->limit($rowperpage)->get();
        $data = array();

        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            // $emp['edit'] = route("reviews.edit",$emp["id"]);
            $emp['show'] = route("reviews.show",$emp["id"]);
            // $emp['delete'] = route("reviews.destroy",$emp["id"]);
            $emp['recipe_name'] = "<a href='".route("blogger_food_detail",$emp->food_blog->id)."'>".$emp->food_blog->recipe_name."</a>";
            $emp['added_by'] = $emp->customer->first_name.' '.$emp->customer->last_name;
            $emp['report_count'] = count($emp->reports);
            $emp['review'] = '<p data-toggle="tooltip" data-placement="top" title="'.$emp->review.'">'.substr($emp->review, 0, 30) .'</p>';
            
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
        $page_title = trans('reviews.show');
        $review = Review::find($id);
        if(!$review) {
          return redirect()->route('reviews.index')->with('error',trans('common.no_data'));
        }
        return view('admin.reviews.show',compact('review','page_title'));
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request)
    {
      // echo "<pre>";print_r("this");exit;
        $review= Review::where('id',$request->id)->first();
        
        $review->update(['status'=>$request->status]);
       if($review) {
        return response()->json(['success' => trans('reviews.status_updated')]);
       } else {
        return response()->json(['error' => trans('reviews.error')]);
       }
    }
}
   