<?php

namespace App\Models\Helpers;

use Illuminate\Support\Facades\Storage;
use DB;
use Image;
use App\Models\Setting;
use App\Models\Notification;
use App\Models\DeviceDetail;
use Edujugon\PushNotification\PushNotification;
use DateInterval;
use DateTime;
use DatePeriod;

trait CommonHelpers
{
  //public variables
  public $menus_path = 'uploads/menus/';
  public $banners_path = 'uploads/banners/';
  public $company_logo_path = 'uploads/company_logo/';
  public $brand_logo_path = 'uploads/brand_logo/';
  public $news_banner_path = 'uploads/news_banner/';
  public $blogger_photo_path = 'uploads/blogger_photo/';
  public $sponsor_logo_path = 'uploads/sponsor_logo/';
  public $recipe_video_path = 'uploads/recipe_video/';
  public $recipe_image_path = 'uploads/recipe_image/';
  public $sponsor_video_path = 'uploads/sponsor_videos/';
  public $menu_item_photo_path = 'uploads/menu_item_photos/';
  public $advertisements_path = 'uploads/advertisements/';
  public $catering_package_image_path = 'uploads/catering_package_image/';
  /**
   * Save different type of media into different folders
   */
  public function saveMedia($file,$type)
  {
      $media = $file;
      $filenameWithExt =  $media->getClientOriginalName();
      $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
      $extension =  $media->getClientOriginalExtension();
      $fileNameToStore = str_replace(' ','_',$filename).'_'.time().'.'.$extension;

      // simple
      if($type == 'menu') {

          $img = Image::make($media->getRealPath());
          $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/menus'.'/'.$fileNameToStore);
          $path =  $this->menus_path.$fileNameToStore;
          return $path;
      }else if($type == 'banner') {
          $img = Image::make($media->getRealPath());
          $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/banners'.'/'.$fileNameToStore);
          $path =  $this->banners_path.$fileNameToStore;
          return $path;
      }else if($type == 'company_logo') {
          $img = Image::make($media->getRealPath());
          $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/company_logo'.'/'.$fileNameToStore);
          $path =  $this->company_logo_path.$fileNameToStore;
          return $path;
      }else if($type == 'brand_logo') {
          $img = Image::make($media->getRealPath());
          $img->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/brand_logo'.'/'.$fileNameToStore);
          $path =  $this->brand_logo_path.$fileNameToStore;
          return $path;
      }
      else if($type == 'news_banner') {
          $img = Image::make($media->getRealPath());
          $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/news_banner'.'/'.$fileNameToStore);
          $path =  $this->news_banner_path.$fileNameToStore;
          return $path;
      }
       else if($type == 'catering_package_image') {
          $img = Image::make($media->getRealPath());
          $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/catering_package_image'.'/'.$fileNameToStore);
          $path =  $this->catering_package_image_path.$fileNameToStore;
          return $path;

      }else if($type == 'blogger_photo'){
          $img = Image::make($media->getRealPath());
          $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/blogger_photo'.'/'.$fileNameToStore);
          $path =  $this->blogger_photo_path.$fileNameToStore;
          return $path;
      }else if($type == 'recipe_video'){
          $save =  $media->move('uploads/recipe_video',$fileNameToStore);
          $path =  $this->recipe_video_path.$fileNameToStore;
          return $path;
      }else if($type == 'recipe_image'){
          $img = Image::make($media->getRealPath());
          $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/recipe_image'.'/'.$fileNameToStore);
          $path =  $this->recipe_image_path.$fileNameToStore;
          return $path;
      }else if($type == 'sponsor_logo'){
          $img = Image::make($media->getRealPath());
          $img->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/sponsor_logo'.'/'.$fileNameToStore);
          $path =  $this->sponsor_logo_path.$fileNameToStore;
          return $path;
      }else if($type == 'sponsor_video'){
          $save =  $media->move('uploads/sponsor_videos',$fileNameToStore);
          $path =  $this->sponsor_video_path.$fileNameToStore;
          return $path;
      }else if($type == 'menu_item_photo') {
          $img = Image::make($media->getRealPath());
          $img->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
          })->save('uploads/menu_item_photos'.'/'.$fileNameToStore);
          $path =  $this->menu_item_photo_path.$fileNameToStore;
          return $path;
      }else if($type == 'advertisements'){
          $save =  $media->move('uploads/advertisements',$fileNameToStore);
          $path =  $this->advertisements_path.$fileNameToStore;
          return $path;
      } else {
          return false;
      }
  }

  public static function quickRandom($length)
  {
      $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
  }

  /**
  * Send Notification
  */
  public function sendNotification($user,$title,$body,$type,$redirect_id=null){
    if($redirect_id == ''){
      $redirect_id = 0;
    }    
    if($user == null){
        return true;
    }

    $sound = 'default';
    switch ($type) {
      case 'admin':
        $redirect_to = 'notifications';
        break;
      case 'coupon':
        $redirect_to = 'notifications';
        break;  
      case 'order_status':
        $redirect_to = 'notifications';
        break;
      case 'waiting_list':
        $redirect_to = 'restaurant';
        $sound = 'restSound.caf';
        break;
      case 'e_wallet_balance':
        $redirect_to = 'e_wallet_history';
        break;
      case 'advertisement':
        $redirect_to = 'advertisement';
        break; 
      case 'sponsor':
        $redirect_to = 'sponsor';
        break; 
      case 'blog':
        $redirect_to = 'blog';
        break;
      case 'rating':
        $redirect_to = 'catering';
        break;            
      default:
        $redirect_to = 'notifications';
        break;
    }

    //Save notification in DB
    $notify           = new Notification();
    $notify->user_id  = $user->id;
    $notify->title    = $title;
    $notify->message  = $body;
    $notify->type  = $type;
    $notify->redirect_to  = $redirect_to;
    if($redirect_id > 0){
      $notify->redirect_id  = $redirect_id;
    }
    $notify->save();

    //Check for user's device details
    $devices = DeviceDetail::where('user_id',$user->id)->orderBy('created_at','DESC')->get();
    foreach ($devices as $device) {
      //Check if Device details available
      if($device != null && $device->device_token != null){

        \Log::info("Notification device id : $device->id, Token: $device->device_token User id : $device->user_id  Completed");

        $badge = Notification::where('status','unread')->where('user_id',$user->id)->count();

        if($device->device_type == 'android'){

          $setting = Setting::pluck('value','name')->all();
          $fcm     = $setting['fcm_server_key'];

          $push = new PushNotification('fcm');
          $message = [         
                          'data' => [
                            'title'    =>  $title,
                            'body'     =>  $body,
                            'sound' => $sound,
                            'badge' => $badge,
                            'redirect_to' => $redirect_to,
                            'id' => $redirect_id,
                            'type' => $type,
                          ]
                      ];
                        
            $push->setMessage($message);
            $push = $push->setApiKey($fcm);            
                
            $push  =  $push->setDevicesToken($device->device_token)
                           ->send()
                           ->getFeedback();

            // return $push;

        //Notification for iOS Devices
        }else{
         //$message = '{"aps":{"alert":"Now!","sound":"default"}}';
            $message = [
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'sound' => $sound,
                    'badge' => $badge,
                    'redirect_to' => $redirect_to,
                    'id' => $redirect_id,
                    'type' => $type,
                ],
            ];
            $push  = $this->sendHTTP2Push($message, $device->device_token);
            dd($push);
            /*$push = new PushNotification('apn');


            // echo json_encode($message); die;

            $push->setMessage($message)
                ->setDevicesToken([
                    $device->device_token
                ]);
                // print_r($push);
                // exit;
            $push = $push->send();*/
          
          
            // return $push->getFeedback();
            // return true;

        }
      }
    }
    return true;
  }

  public function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
    // Declare an empty array 
    $array = array(); 
      
    // Variable that store the date interval 
    // of period 1 day 
    $interval = new DateInterval('P1D'); 
  
    $realEnd = new DateTime($end); 
    $realEnd->add($interval); 
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
  
    // Use loop to store date into array 
    foreach($period as $date) {                  
        $array[] = $date->format($format);  
    } 
  
    // Return the array elements 
    return $array; 
  } 

  public function sendHTTP2Push($message, $token) {
    $http2ch = curl_init();
    curl_setopt($http2ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

    $milliseconds = round(microtime(true) * 1000);

    // url (endpoint)
    if(env('APP_ENV') == 'development'){
      $http2_server = 'https://api.development.push.apple.com:443';
    } else {
      $http2_server = 'https://api.push.apple.com:443';
    }
    $url = "{$http2_server}/3/device/55cd5d158d2c9c5ef4c5b32b120bcb01b384868ed897fe976a67002e593e9353";

    // certificate
    $cert = realpath('iosCertificates\waitinglist.pem');
    $app_bundle_id = 'clueapps.com.WaitingList';

    /*if(!$cert || !is_readable($cert)){
        die("error: myfile.pem is not readable! realpath: \"{$cert}\" - working dir: \"".getcwd()."\" effective user: ".print_r(posix_getpwuid(posix_geteuid()),true));
    }*/
    // headers
    $headers = array(
        "apns-topic: {$app_bundle_id}",
        "User-Agent: My Sender"
    );

    // other curl options
    curl_setopt_array($http2ch, array(
        CURLOPT_URL => "{$url}",
        CURLOPT_PORT => 443,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => json_encode($message),
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSLCERT => $cert,
        CURLOPT_HEADER => 1
    ));

    // go...
    $result = curl_exec($http2ch);
    if ($result === FALSE) {
      throw new \Exception('Curl failed with error: ' . curl_error($http2ch));
    }
    // get respnse
    dd($result);
    $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);

    $duration = round(microtime(true) * 1000) - $milliseconds;

    curl_close($http2ch);
    return $status;
  }
}