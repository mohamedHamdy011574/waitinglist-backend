<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FoodBlog;
use App\Models\Blogger;
use App\Models\Cuisine;
use App\Models\User;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\Helpers\CommonHelpers;

class FoodBlogController extends Controller
{   

    use CommonHelpers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:blog-list', ['only' => ['index','show']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // blogger_list 
    public function blogger_index()
    {
       $page_title = trans('bloggers.heading'); 
       $bloggers = Blogger::all();
       return view('admin.bloggers.index',compact(['bloggers','page_title']));
    }

    public function blogger_index_ajax(Request $request)
    {
        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        $query = new Blogger();   
    
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter = $query;

        if($searchValue != ''){
        $filter = $filter->where(function($q)use ($searchValue) {
                            $q->where('blogger_name','like','%'.$searchValue.'%')
                            // ->orWhere('dial_code','like','%'.$searchValue.'%')
                            ->orWhere('blogger_photo','like','%'.$searchValue.'%')
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
            $emp['blogger_photo'] = '<img src="'.asset(Blogger::where('id',$emp->id)->value('blogger_photo')).'" style="width:50px">';
            $emp['show']= route("blogger_post",$emp["id"]);    
            $emp['total_blogs']=FoodBlog::where('added_by',$emp['id'])->count();
            
            
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

    public function blogger_post($id)
    {
        // print_r($id);die;
        $id = $id;
        $blogger = Blogger::find($id);
        $page_title = trans('food_blogs.heading',['blogger' => $blogger->blogger_name]); 
        // echo "<pre>";print_r($blogger);exit;
        // print_r($ids);die; 
        return view('admin.food_blogs.index',compact('id','page_title','blogger'));
    }

// food blog
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
        $id = $request['id'];

        $query = FoodBlog::where('added_by',$id);   
    
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;


        ## Total number of record with filtering
        $filter = $query;

        if($searchValue != ''){
           $filter = $filter->where(function($q)use ($searchValue) {
                        $q->whereHas('cuisine',function($query) use ($searchValue){
                            $query->whereHas('translation',function($query) use ($searchValue){
                                $query->where('name','like','%'.$searchValue.'%');
                                });
                            })
                            ->orWhere('recipe_name','like','%'.$searchValue.'%')
                            ->orWhere('created_at','like','%'.$searchValue.'%')
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
           
         $date = date($emp->created_at->todatestring());
         $emp['date'] = date("d-M-Y", strtotime($date));
         $emp['cuisine_id']= $emp->cuisine->name;
         $emp['show']= route("blogger_food_detail",$emp["id"]); 

            
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
// food blog detaail
    public function blogger_food_detail($id)
    {
        $page_title = trans('food_blogs.blod_details'); 
        $food_detail= FoodBlog::find($id);
        $blogger_name = Blogger::where('id',$food_detail->added_by)->value('blogger_name');
        return view('admin.food_blogs.food_detail',compact('food_detail','page_title','blogger_name'));
    }

    /**
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function status(Request $request)
    {
        $food_blog = FoodBlog::find($request->id);

       if($food_blog) {
        $food_blog->update(['status'=>$request->status]);
        //send notification
        if($request->status == 'active'){
          // echo $food_blog->blogger->customer_id; die;
          $user = User::find(@$food_blog->blogger->customer_id);
          if($user) {
            $title = trans('food_blogs.food_blog_status');
            $body = trans('food_blogs.blog_approved');
            $this->sendNotification($user,$title,$body,"blog",$food_blog->id);
          }
        }
        return response()->json(['success' => trans('food_blogs.status_updated')]);
       } else {
        return response()->json(['error' => trans('food_blogs.error')]);
       }
    }
}
