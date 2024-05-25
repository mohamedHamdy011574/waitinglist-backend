<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:setting-list', ['only' => ['index','update']]);
    }
    public function index() {
        $settings_data = Setting::pluck('value','name');
        return view('admin.settings.index',compact('settings_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
 


    public function update(Request $request) {
        $settings = $request->all();
        $vreq = [];
        foreach ($settings as $sk => $sv) {
            if($sk == 'from_email'){
                $validation = 'required|email|max:150';
            }
            else if($sk == 'contact_no'){
                $validation = 'required|max:14';
            }else{
                $validation = 'required';
            }
            $vreq[$sk] = $validation;
        }
        $request->validate($vreq);

        foreach ($settings as $key => $value) {
        Setting::where('name', $key)->update(['value'=> $value]);
        }  
        return redirect()->back()->with('success','update Successfully'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
}
