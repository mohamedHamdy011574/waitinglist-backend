<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\Country;
use App\Models\Helpers\CommonHelpers;

class CompanyDetailController extends Controller
{
    use CommonHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_detail = [];
        $company_detail['company_logo'] = CompanyDetail::where(['name'=>'company_logo'])->select(['value'])->first();
        $company_detail['company_name'] = CompanyDetail::where(['name'=>'company_name'])->select(['value'])->first();
        $company_detail['GSTIN'] = CompanyDetail::where(['name'=>'GSTIN'])->select(['value'])->first();
        $company_detail['contact_number'] = CompanyDetail::where(['name'=>'contact_number'])->select(['value'])->first();
        $company_detail['company_email'] = CompanyDetail::where(['name'=>'company_email'])->select(['value'])->first();
        $company_detail['customer_care_email'] = CompanyDetail::where(['name'=>'customer_care_email'])->select(['value'])->first();
        $company_detail['company_address'] = CompanyDetail::where(['name'=>'company_address'])->select(['value'])->first();
        $company_detail['country'] = CompanyDetail::where(['name'=>'country'])->select(['value'])->first();

        $country = Country::where('status','active')->get();
        $page_title = trans('company_detail.heading'); 
        return view('admin.company_details.index',compact('company_detail','country' ,'page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
         $request->validate([
            'company_name' => 'required',
            'GSTIN' => 'required',
            'contact_number' => 'required|max:14',
            'company_email' => 'required|email|max:32',
            'customer_care_email' => 'required|email|max:32',
            'company_address' => 'required',
            'country' => 'required',       
        ]);

        $company_detail = $request->all();
        if($request->has('company_logo')){
            $path = $this->saveMedia($request->company_logo,'company_logo');
            $company_detail['company_logo'] = $path;
        } 
    
        foreach ($company_detail as $key => $value) {
        CompanyDetail::where('name', $key)->update(['value'=> $value]);
        }  
        return redirect()->back()->with('success',trans('company_detail.update')); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
}
