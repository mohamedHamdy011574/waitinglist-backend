<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms;
use App\Models\Translations\CmsTranslation;
use Auth;


class TermsConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed', 'vendor_business_details']);
        $this->middleware('permission:business-create', ['only' => ['terms_and_conditions','update_terms_and_conditions']]);
    }
  
    public function create(){
        $page_title = trans('cms.terms_and_conditions');
        $user = Auth::user();
        $business = @$user->business;
        $cms = Cms::where(['slug' => 'terms_and_conditions', 'business_id' => $business->id])->first();
        if(!$cms){
          $terms_conditions_data = [
            'page_name:en' => 'Terms and Conditions',
            'content:en' => '<p></p>',
            'page_name:ar' => 'Terms and Conditions',
            'content:ar' => '<p></p>',
            'slug' => 'terms_and_conditions',
            'display_order' => 1,
            'status'  => 1,
            'business_id' => @$business->id,
          ];
          $cms = Cms::create($terms_conditions_data);
        }
        return redirect()->route('terms_and_conditions.edit',$cms->id);
        // return view('admin.cms.terms_and_conditions',compact('page_title','cms'));
    }

    public function edit($id) {
      $page_title = trans('cms.terms_and_conditions');
      $user = Auth::user();
      $business = @$user->business;
      $cms = Cms::where([ 'id' => $id , 'slug' => 'terms_and_conditions', 'business_id' => $business->id])->first();
      if(!$cms) {
        return redirect()->route('terms_and_conditions.create');
      }
      return view('admin.cms.terms_and_conditions',compact('page_title','cms'));
      // print_r($request); die;
    }

    public function update(Request $request, $id)
    {
      $user = Auth::user();
      $business = @$user->business;
      $cms = Cms::where([ 'id' => $id , 'slug' => 'terms_and_conditions', 'business_id' => $business->id])->first();
      if(!$cms) {
        return redirect()->route('terms_and_conditions.create');
      }
      $cms->update($request->all());
      return redirect()->route('terms_and_conditions.edit',$cms->id)->with('success',trans('cms.terms_and_conditions_success'));
    }
}
   