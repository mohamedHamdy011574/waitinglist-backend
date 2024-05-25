<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\Helpers\CommonHelpers;
use Auth;
use Validator,DB;


class TestController extends BaseController
{
	
    use CommonHelpers;


    /**
    * Send Test Notification
    * @return \Illuminate\Http\Response
    */
    public function send_notification(Request $request){

        $user = Auth::user();

        $title    = 'Test Title';
        $body     = 'Test Notification Message';
      
        $response = $this->sendNotification($user,$title,$body,"admin","");

        return \Response::json($response);
        
    }

}   
