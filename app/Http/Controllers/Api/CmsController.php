<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Cms;
use App\Http\Resources\CmsResource;
use App\Models\Setting;

class CmsController extends BaseController
{	
	public function page($slug = '') {
		if(!$slug || $slug == '') {
        return $this->sendError('',trans('cms.slug_required'));
    }

	  $page = Cms::where('slug',$slug)->first();
    
    if($page) {
      if($page->slug == 'contact_us') {
        $page->content = [
          'email' => Setting::get('from_email'),
          'instagram' => Setting::get('instagram_url'),
          'twitter' => Setting::get('twitter_url'),
        ];
      } 
      return $this->sendResponse(New CmsResource($page),trans('cms.page_got_successfully'));
    } else {
      return $this->sendError('',trans('cms.page_not_found')); 
    }
  }

  public function terms_and_conditions($business_id) {
    $page = Cms::where(['slug' => 'terms_and_conditions',  'business_id' => $business_id])->first();
    if($page) {
      return $this->sendResponse(New CmsResource($page),trans('cms.page_got_successfully'));
    } else {
      return $this->sendError('',trans('cms.page_not_found')); 
    }
  }
}
