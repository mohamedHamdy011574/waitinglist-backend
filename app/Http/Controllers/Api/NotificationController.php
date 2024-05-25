<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Auth;
use Carbon\Carbon;

class NotificationController extends BaseController
{
    public function notification(){
        $user = Auth::user();
        if($user == null){
            return $this->sendError($this->array,trans('filter_data.user'));
        }
        // read all notification
        $notification_read = array();
        $notification_read['status'] = 'read';
        $read = Notification::where('user_id',$user->id)->update($notification_read);

        $notification = NotificationResource::collection(Notification::where('user_id' ,$user->id)
        	->latest()
        	->orderBy('status','DESC')
        	->paginate());
  

        if($notification) {
	        if($notification->count()){
	          return $this->sendPaginateResponse($notification,trans('notifications.notification_success'));
	        }else{
	          return $this->sendPaginateResponse($notification,trans('notifications.notification_error'));
	        }
       }else {
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
       
    }

    public function notification_count(){
        $user = Auth::user();
        if($user == null) {
            return $this->sendError($this->array,trans('notification.ggr'));
        }
 		    $notification = array();
       	$notification['count'] = Notification::where('status','unread')->where('user_id',$user->id)->count();
         if($notification != null) {
            return $this->sendResponse($notification,trans('notifications.notification_count'));
        } else {
            return $this->sendError($this->array,trans('filter_data.error'));
        }
    }

    public function delete_notifications() {
      $user = Auth::user();
      if($user == null){
          return $this->sendError($this->array,trans('filter_data.user'));
      }
      // delete all notification
      $delete = Notification::where('user_id',$user->id)->delete();
      return $this->sendResponse('',trans('notifications.deleted'));
    }
}

