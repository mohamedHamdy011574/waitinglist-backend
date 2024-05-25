<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        $this->middleware('auth');
        // $this->middleware('permission:setting-list', ['only' => ['index','update']]);
    }
    public function index() {
        $user_profile = auth()->user();
        return view('admin.profile.index',compact('user_profile'));
    }

    /**
     * Update user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {

      $validator= $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,id'],
            'phone_number' => ['required', 'numeric','min:10', 'unique:users,id,'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ]);
      

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ];

        if($request->has('password') && $request->password != '') { 
          // $request->request->add([
          //   'password' => Hash::make($request->password),
          // ]);
          $data['password'] = Hash::make($request->password);
        }

        // print_r($data); die;

        $admin = auth()->user();

        // echo '<pre>'; print_r($data); die;

        DB::beginTransaction();
        if($admin) {
          $admin->update($data);
          DB::commit();
          return redirect()->route('home')->with('success',trans('profile.updated'));
        }else{
          DB::rollback();
          return redirect()->route('home')->with('error',trans('profile.error'));
        }
    }

  
}
