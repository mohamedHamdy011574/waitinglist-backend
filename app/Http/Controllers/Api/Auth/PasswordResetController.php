<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class PasswordResetController extends BaseController
{
    /**
     * Reset password
     * @return \Illuminate\Http\Response
     */
    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'password'   => 'required|max:15|min:6',
            'confirm_password' => 'required|max:15|min:6|same:password',
        ]);

        if($validator->fails()){
            return $this->sendValidationError('',$validator->errors()->first());       
        }

        $user = auth()->user();
        $phone = $user->phone_number;
        $input = $request->all();
    
        if (!$user){
        	return $this->sendError('',trans('customer_api.reset_password_user'));
        }

        $password = $input['password'];
        $user->password = bcrypt($password);
        $user->is_password_change = 1;
       
        if($user->save()){
            //$request->user()->token()->revoke();
            return $this->sendResponse('',trans('customer_api.reset_password_success'));
        } else {
            return $this->sendError('',trans('customer_api.reset_password_error'));
        }
        
    }

    /**
     *  Forgot Password
     * @return \Illuminate\Http\Response
     */
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'phone_number' => 'required|digits_between:8,10',
            'country_code' => 'required|string|max:3',
        ]);

        if($validator->fails()){
            return $this->sendValidationError('',$validator->errors()->first());       
        }

        $dataArray = $request->all();
        $user = User::where('phone_number',$dataArray['phone_number'])->first();
        if(empty($user)){
            return $this->sendError('', trans('customer_api.forgot_password_user'));
        }       
        if(Settings::has('twilio_sid')){
            $twilio_id = Settings::get('twilio_sid');
        } else {
            $twilio_id = config('services.twilio.sid');
        }
        if(Settings::has('twilio_auth_token')){
            $twilio_token = Settings::get('twilio_auth_token');
        } else {
            $twilio_token = config('services.twilio.token');
        }
        if(Settings::has('twilio_from')){
            $twilio_number  = Settings::get('twilio_from');
        } else {
            $twilio_number  = config('services.twilio.from');
        }

        $password = rand(100000, 999999);
        $user->password = bcrypt($password);
        
        try
        {
            $message ='Your new password is: '.$password;
            $twilio = new Client($twilio_id, $twilio_token);

            $message = $twilio->messages
                      ->create($user->phone_number, 
                       [
                           "body" => $message,
                           "from" => $twilio_number
                       ]);

            if($user->save()){
                return $this->sendResponse('', trans('customer_api.forgot_password_success'));
            }else{
                return $this->sendError('', trans('customer_api.forgot_password_error')); 
            }
               
        }
        catch (\Exception $e)
        { 
          return $this->sendError('', $e->getMessage()); 
        }
    }
}
