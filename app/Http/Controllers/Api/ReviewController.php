<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Concern;
use App\Models\Report;
use App\Models\FoodBlog;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\ConcernResource;
use DB,Validator,Auth;

class ReviewController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Food Blog Reviews
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        try{
            $validator = Validator::make($request->all(),[
                'blog_id' => 'required'
            ]);

            $blog = FoodBlog::find($request->blog_id);
            if(!$blog){
              return $this->sendError('',trans('food_blogs.not_found')); 
            }

            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $blog_reviews = Review::where('blog_id',$request->blog_id)->where('status','active')->paginate();

            if($blog_reviews) {
                if($blog_reviews->count()) {
                    return $this->sendPaginateResponse(ReviewResource::collection($blog_reviews),trans('reviews.blog_review_success'));
                } else {
                    return $this->sendPaginateResponse(ReviewResource::collection($blog_reviews),trans('reviews.not_found'));
                }
            } else {
                return $this->sendError('',trans('common.something_went_wrong')); 
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage()); 
        }
    }

    /**
     * Add Review on Food Blog.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_review(Request $request) {
        try{
            $validator = Validator::make($request->all(),[
                'review'  => 'required|min:3|max:100',
                'blog_id' => 'required|exists:food_blogs,id'
            ]);
            if($validator->fails()){
            return $this->sendValidationError('', $validator->errors()->first());
            }
            $data = [
                'given_by' => Auth::user()->id,
                'blog_id' => $request->blog_id,
                'review' => $request->review
            ];

            $review = Review::create($data);

            if($review)
            {
                return $this->sendResponse(new ReviewResource($review),trans('reviews.review_added')); 
            }
            else
            {
                return $this->sendError('',trans('common.something_went_wrong')); 
            }
        }catch(\Exception $e){
          return $this->sendError('',$e->getMessage()); 
        }
    }

    /**
     * Concern List for report review.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function concern_list(Request $request) {
        try{
            $concerns = Concern::where('status','active')
                  ->join('concern_translations', 'concern_translations.concern_id', '=', 'concerns.id')->orderBy('concern_translations.concern','asc')
                  ->where('concern_translations.locale',\App::getLocale())
                  ->select(['concerns.*'])
                  ->get();

            // $concerns = Concern::where('status','active')->get();

            if($concerns) {
                return $this->sendResponse(ConcernResource::collection($concerns),trans('reviews.concerns'));
            } else {
                return $this->sendError('',trans('common.something_went_wrong')); 
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage()); 
        }
    }

    /**
     * Report Review.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function report_review(Request $request) {
        try{
            $validator = Validator::make($request->all(),[
                'review_id'  => 'required|exists:reviews,id',
                'concern_id' => 'required|exists:concerns,id',
                'comment' => 'required|min:3|max:100',
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());
            }
            $report_exist = Report::where('reported_by',Auth::user()->id)->where('review_id',$request->review_id)->first();

            if($report_exist)
            {
                return $this->sendError('', trans('reviews.reported_already'));
            }
            $data = [
                'reported_by' => Auth::user()->id,
                'review_id' => $request->review_id,
                'concern_id' => $request->concern_id,
                'comment' => $request->comment
            ];

            $report = Report::create($data);

            if($report)
            {
                return $this->sendResponse(new ReportResource($report),trans('reviews.report_added')); 
            }
            else
            {
                return $this->sendError('',trans('common.something_went_wrong')); 
            }
        }catch(\Exception $e){
          return $this->sendError('',$e->getMessage()); 
        }
    }
}
