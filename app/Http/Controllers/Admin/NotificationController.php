<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use App\Mail\MassEmail;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use LaravelFCM\Message\Topics;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Helpers\CommonHelpers;

class NotificationController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){   
        $page_title = trans('notifications.heading');
        $users =  User::where([
                'user_type' => 'Customer', 
                'status' => 'active', 
                'verified' => 1
            ])->get();
        return view('admin.notifications.index',compact(['users','page_title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function sendNotificationToUsers(Request $request){
        $validator = $request->validate([
            'title'=>'required|min:3|max:150',
            'message'=>'required',
            'message_type'=>'required',
            'customer_select'=>'required',
        ]);
        $data = $request->all();
        // print_r($data); die;
        $notification_message   = $data["message"];
        $message_type           = $data["message_type"];
        // $sendAll = $notification["customer_select"];
        /*if($request['message_type'] && $request['customer_select']){
            $message_type = $data["message_type"];
        } else if($request['customer_select']){
            $sendAll = $data["customer_select"];
        }*/
        
        $users = User::where('user_type','Customer')
                    ->whereIn('id',$data['customer_select'])
                    ->where(['verified' => 1, 'status' => 'active'])
                    ->get();
        // echo '<pre>'; print_r($users); die;
        if($message_type == "notification"){
            $title = $request->title;
            foreach ($users as $user) {
                $nres = $this->sendNotification($user,$title,$notification_message,"admin","");
                // echo '<pre>'; print_r($nres); die;
            }
            return redirect()->route('notifications.index')->with('success',trans('notifications.send_notification_success'));
        }
        //return redirect()->route('notifications.index')->with('error', trans('notification.notification_error'));  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
 
}
