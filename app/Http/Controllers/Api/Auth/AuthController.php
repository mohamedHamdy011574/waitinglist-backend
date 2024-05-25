<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\DeviceDetail;
use App\Models\MobileVerification;
use App\Models\PasswordResetCode;
use App\Http\Resources\UserResource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
  /**
   * Signup api
   * @return \Illuminate\Http\Response
   */
  public function signup(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'first_name'       => 'required|min:2|max:50',
          'last_name'       => 'required|max:50',
          'email'            => 'required|email|max:150',
          'password'         => 'required|min:6|max:18',
          'phone_number'    => 'required|min:8|max:13',
          'birth_date'    => 'sometimes|date|date_format:Y-m-d',
          'device_token' => 'required',
          'device_type' => 'required',
          //'phone_number'    => 'required|unique:users|digits_between:8,15',
          //'country_id'       => 'required|exists:countries,id',
          // 'state_id'         => 'sometimes|nullable|exists:states,id',
          // 'city_id'          => 'sometimes|nullable|exists:cities,id',
          // 'gender'           => 'sometimes|nullable|in:Male,Female,Other',
          // 'image'            => 'sometimes|nullable|image|max:10000'
      ]);
      if($validator->fails()){
          return $this->sendValidationError('', $validator->errors()->first());
      }

      // print_r($request->all()); die;
      if($request->device_type == 'ios' || $request->device_type == 'android'){
      }else{
        return $this->sendError('',trans('auth.device_type_error'));
      }

      //Checking for duplicates
      $user_exists = User::where(['email' =>  $request->email, 'verified' => 1])->count();
      if($user_exists) { // Duplicate entry
          return $this->sendError('', trans('auth.email_already_exists'));
      }

      $user_phone_exists = User::where(['phone_number'=> $request->phone_number, 'verified' => 1])->count();
      // print_r($user_phone_exists); die;
      if($user_phone_exists) { // Duplicate entry
          return $this->sendError('', trans('auth.you_can_login'));
      }


      $input = $request->all();
      $input['password'] = bcrypt($input['password']);
      // $input['email_verify_token'] = $this->quickRandom(60); 
      $input['user_type'] = 'Customer';
      // Create New User Eloquent Instance
      $user = new User();
      $user->fill($input);

      DB::beginTransaction();
      try {
          if($user->save()){ // Check if user data is saved
              $save_device = $this->save_device($user->id, $input['device_type'], $input['device_token']);
              $send_otp = $this->send_otp($user);
              // print_r($response); die;
              $response = [
                            'user'=> new UserResource($user),
                            'otp_details' => $send_otp
                          ];
              DB::commit();
              return $this->sendResponse($response, trans('auth.registered_verify_otp'));
          }else{
              DB::rollback();
              return $this->sendError($this->object,trans('auth.error'));
          }
      } catch (Exception $e) {
        DB::rollback();
        return $this->sendException($this->object,$e->getMessage());
      }   
  }
    
  /**
   * Login api
   *
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request)
  {
      // print_r($request->all()); die;
      $validator = Validator::make($request->all(), [
          'phone_number' => 'required|min:8|max:13',
          'password' => 'required|min:6|max:18',
          'device_token' => 'required',
      ]);
      if($validator->fails()){
          return $this->sendValidationError('', $validator->errors()->first());
      }

      // prechecking User
      $userPreCheck = User::where(['phone_number' => $request->phone_number, 'user_type'=>'Customer'])->latest()->first();

      // if($userPreCheck->verified == 0){
      //   return $this->sendError([], 'Sorry!, You are not verified User.');
      // }
      if($userPreCheck){
        if($userPreCheck->status == 'blocked' || $userPreCheck->status == 'inactive')
        {
                return $this->sendError([], trans('auth.user_inactive'));
        }
      }

      //Auth::logoutOtherDevices($request->password);
      if(Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password,'user_type'=>'Customer', 'verified' => 1])){
          //return $user;
          $user = Auth::user();
          //Add response details into variable
          $success['token']  =  $user->createToken(config('app.name'))->accessToken;
          $success['user']   =  new UserResource($user);

          $data = $request->except('phone_number','password','user_type');

          $createArray = array();

          foreach ($data as $key => $value) {
              $createArray[$key] = $value;
          }

          $device_detail = DeviceDetail::where('user_id',Auth::user()->id)->first();

          $device_exists = DeviceDetail::where([
            'user_id' => Auth::user()->id,
            'device_token' => $createArray['device_token']
          ])->get()->count();
          if($device_exists){

          }else{
            $createArray['user_id'] = Auth::user()->id;
            DeviceDetail::create($createArray);
          }
          /*// print_r($device_detail);
          print_r($createArray); die;
          if($device_detail){
              $device_detail->update($createArray);
          } else {
              $createArray['user_id'] = Auth::user()->id;
              DeviceDetail::create($createArray);
          }*/



          return $this->sendResponse($success, trans('auth.login_success'));
      }  else {
          return $this->sendError('',trans('auth.login_error'));
      } 
  }

  public function change_password(Request $request)
  {
      $input = $request->all();
      $userid = Auth::guard('api')->user()->id;
      $rules = array(
          'old_password' => 'required|min:6|max:18',
          'new_password' => 'required|min:6|max:18',
          'confirm_password' => 'required|same:new_password',
      );
      $validator = Validator::make($input, $rules);
      if($validator->fails()){
          return $this->sendValidationError('', $validator->errors()->first());       
      } else {
          try {
              if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                  $arr = $this->sendError([], trans('auth.old_password_wrong'));
              } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                  $arr = $this->sendError([], trans('auth.pasword_same_as_current'));
              } else {
                  User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                  $arr = $this->sendResponse([], trans('auth.password_success'));
              }
          } catch (Exception $ex) {
              if (isset($ex->errorInfo[2])) {
                  $msg = $ex->errorInfo[2];
              } else {
                  $msg = $ex->getMessage();
              }
              $arr = $this->sendError([], $msg);
          }
      }
      return $arr;
  }

  /**
   * Logout user (Revoke the token)
   *
   * @return [string] message
   */
  public function logout(Request $request)
  {
      // print_r($request->all()); die;
      $validator = Validator::make($request->all(), [
          'device_token' => 'required',
      ]);
      if($validator->fails()){
          return $this->sendValidationError('', $validator->errors()->first());
      }
      
      $user = Auth::user();
      if($user){
          /*$device_detail = $user->device_detail;
          if($device_detail){
              $device_detail->delete();
          }*/
          DeviceDetail::where('device_token', $request->device_token)->delete();
          $user->token()->revoke();    
      }
      
      return $this->sendResponse('', trans('auth.logout_success'));
  }


  /**
   * mobile_activation
   *
   * @return [string] message
   */
  public function mobile_activation(Request $request)
  {
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required|exists:users,phone_number',
        'code' => 'required',
    ]);

    if($validator->fails()){
        return $this->sendValidationError('', $validator->errors()->first());
    }

    $mobile_verification = MobileVerification::where([
        'phone_number' => $request->phone_number,
        'code' => $request->code,
    ])->first();

    if(!$mobile_verification){
      return $this->sendError('', trans('auth.invalid_otp'));
    }

    $user_data = User::find($mobile_verification->user_id);

    if($mobile_verification->isExpired()){
      $resent = $this->send_otp($user_data);
      if($resent){
        return $this->sendError($resent, trans('auth.otp_expired_use_new'));
      }else{
        return $this->sendError('', trans('auth.otp_expired'));
      }
    }

    $user_data->verified = 1;
    $user_data->save();

    $success['token']  =  $user_data->createToken(config('app.name'))->accessToken;
    $success['user']   =  new UserResource($user_data);

    MobileVerification::where('phone_number', $user_data->phone_number)->delete();
    return $this->sendResponse($success, trans('auth.login_success'));
  }

  /**
   * resend_otp
   *
   * @return [string] message
   */
  public function resend_otp(Request $request)
  {
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required|exists:users,phone_number',
    ]);
    if($validator->fails()){
        return $this->sendValidationError('', $validator->errors()->first());
    }

    $user = User::where('phone_number', $request->phone_number)->latest()->first();
    if($user){
      $send_otp = $this->send_otp($user);
      if($send_otp){
        return $this->sendResponse($send_otp, trans('auth.otp_sent'));
      }
    }else{
      return $this->sendError('', trans('auth.user_not_exists'));
    }
  }

  /**
   * forgot_password
   *
   * @return [string] message
   */
  public function forgot_password(Request $request)
  {
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required|exists:users,phone_number',
    ]);

    if($validator->fails()){
        return $this->sendValidationError('', $validator->errors()->first());
    }

    $user = User::where(['phone_number' => $request->phone_number, 'verified' => 1])->first();
    if($user) {
      $send_frgtp = $this->send_forgot_password_otp($user);
      if($send_frgtp){
        return $this->sendResponse($send_frgtp, trans('auth.forgot_password_otp_sent'));
      }
    }else{
      return $this->sendError('', trans('auth.password_reset_success'));
    }
  }

  /**
   * forgot_password
   *
   * @return [string] message
   */
  public function reset_password(Request $request)
  {
    $validator = Validator::make($request->all(), [
          'phone_number' => 'required|exists:users,phone_number',
          'code' => 'required',
          'password' => 'required|min:6|max:18',
          'confirm_password' => 'required|same:password',
      ]);

      if($validator->fails()){
          return $this->sendValidationError('', $validator->errors()->first());
      }

    $password_reset_code = PasswordResetCode::where([
        'phone_number' => $request->phone_number,
        'code' => $request->code,
    ])->first();

    // print_r($password_reset_code); die;

    if(!$password_reset_code){
      return $this->sendError('', trans('auth.invalid_otp'));
    }
    
    User::where('id', $password_reset_code->user_id)->update(['password' => Hash::make($request->password)]);

    PasswordResetCode::where('phone_number', $request->phone_number)->delete();
    return $this->sendResponse([], trans('auth.password_reset_success'));
  }

   
}