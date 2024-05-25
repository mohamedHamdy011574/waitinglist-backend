<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Models\DeviceDetail;
use App\Models\MobileVerification;
use App\Models\PasswordResetCode;
use App\Models\Notification;
use App\Models\Setting;
use Twilio\Rest\Client;
use Authy\AuthyApi;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

use Edujugon\PushNotification\PushNotification;
use App\Models\MessageLog; // For message report


class BaseController extends Controller
{
  /**
   * success response method.
   *
   * @return \Illuminate\Http\Response
   */
  public function sendResponse($result = [],$message,$status= '200'){
  	$response = [
                  'success' => "1",
                  'status'  => $status,
                  'message' => $message, 
                ];

    if(!empty($result)){
      $response['data'] = $result;
    }
    return response()->json($response, 200);
  }
  /**
   * return error response.
   *
   * @return \Illuminate\Http\Response
   */
  public function sendError($result = [],$message, $code = 200 , $status= '201'){
    $response = [
                  'success' => "0",
                  'status'  => $status,
                  'message' => $message,
                ];

    if(!empty($result)){
        $response['data'] = $result;
    }
    return response()->json($response, $code);
  }
  /**
  * return validation error response.
  *
  * @return \Illuminate\Http\Response
  */
  public function sendValidationError($result = [],$message, $code = 200 , $status= '201'){
  	$response = [
                  'success' => "0",
                  'status'  => $status,
                  'message' => $message,
                ];

    if(!empty($result)){
      $response['data'] = $result;
    }

    return response()->json($response, $code);
  }
  /**
   * special conditions
   *
   * @return \Illuminate\Http\Response
   */
  public function sendException($result = [],$message,$status= '201'){
    $response = [
        'success' => "1",
        'status'  => $status,
        'message' => $message, 
    ];

    if(!empty($result)){
        $response['data'] = $result;
    }

    return response()->json($response, 200);
  }
  
  public function randomPassword($length = 8) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }


  /**
   * success response method.
   *
   * @return \Illuminate\Http\Response
   */
  public function sendPaginateResponse($result = [],$message,$status= '200'){
    // return $result;
    $response = [
                  'success' => "1",
                  'status'  => $status,
                  'message' => $message, 
                ];

    if(!empty($result)){
      $response['data'] = $result->items();
      $response['links'] = [
          'first' => $result->url(1),
          'last' => $result->url($result->lastPage()),
          'prev' => ($result->previousPageUrl()) ? $result->previousPageUrl() : "",
          'next' => ($result->nextPageUrl()) ? $result->nextPageUrl() : "",
        ];
      $response['meta'] = [
          'current_page' => $result->currentPage(), 
          // 'from' => '', 
          'last_page' => $result->lastPage(), 
          // 'path' => $result->url(1), 
          'per_page' => $result->perPage(), 
          // 'to' => '', 
          'total' => $result->total(), 
        ];
    }
    return response()->json($response, 200);
  }

  /**
  * save device token.
  *
  * @return \Illuminate\Http\Response
  */
  public function save_device($user_id, $device_type, $device_token) {

    $device_exists = DeviceDetail::where([
      'user_id' => $user_id,
      'device_token' => $device_token,
    ])->get()->count();
    $device_detail = false;
    if($device_exists){

    }else{
      $createArray = [
        'user_id' => $user_id,
        'device_type' => $device_type,
        'device_token' => $device_token,
      ];

      $device_detail = DeviceDetail::create($createArray);
    }
    if($device_detail){
      return true;
    }else{
      return false;
    }
  }

  /**
     * Generate a 5 digit code.
     *
     * @return string
     */
  public function getOTPCode() {
      return mt_rand(10000, 99990);
  }

  /**
   * Generatring and sending OTP
   *
   * @return string
  */
  public function send_otp($user) {
    MobileVerification::where('phone_number', $user->phone_number)->delete();
    $code = $this->getOTPCode();
    $mobile = MobileVerification::create([
      'user_id' => $user->id,
      'phone_number' => $user->phone_number,
      'code' => $code,
    ]);

    // $this->send_otp_sms($user, $code);  //send smsbox otp
    return ['remaining_seconds' => $mobile->remainingUntilCodeExpireBySec(), 'otp' => $code];
    //send OTP to mobile number
  }

    /**
   * Generatring and sending OTP
   *
   * @return string
  */
  public function send_forgot_password_otp($user) {
    PasswordResetCode::where('phone_number', $user->phone_number)->delete();
    $code = $this->getOTPCode();
    $mobile = PasswordResetCode::create([
      'user_id' => $user->id,
      'phone_number' => $user->phone_number,
      'code' => $code,
    ]);
    // $this->send_otp_sms($user, $code); //send smsbox otp
    return ['otp' => $code];
    // return true;
    // return $mobile->remainingUntilCodeExpireBySec();
    //send OTP to mobile number
  }

  public function send_otp_sms($user, $code) {
    $text = "WaitingList-OTP:{$code}";
    $smsGateway_url = Setting::get("smsGateway_url");
    $smsGateway_username = Setting::get("smsGateway_username");
    $smsGateway_password = Setting::get("smsGateway_password");
    $smsGateway_custId   = Setting::get("smsGateway_custId");
    $smsGateway_senderText = Setting::get("smsGateway_senderText");;

    $body      = $text;
    $mobile_number = $user->phone_number;

    /*$data = [
      'username' => $smsGateway_username,
      'password' => $smsGateway_password,
      'customerId' => $smsGateway_custId,
      'senderText' => $smsGateway_senderText,
      'messageBody' => $body,
      'recipientNumbers' => $mobile_number,
      'defDate' => null,
      'isBlink' => false,
      'isFlash' => false,
    ];*/

    $url = $smsGateway_url.'?username='.$smsGateway_username.'&password='.$smsGateway_password.'&customerId='.$smsGateway_custId.'&senderText='.$smsGateway_senderText.'&messageBody='.$body.'&recipientNumbers='.$mobile_number.'&defdate=&isBlink=false&isFlash=false';
    // $url = $smsGateway_url;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    // curl_setopt($curl, CURLOPT_POST, true);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($curl);  
    // print_r($response); die;  
    curl_close($curl);
    return true;
  }

  
  



}
