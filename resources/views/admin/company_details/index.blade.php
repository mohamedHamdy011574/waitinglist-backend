@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{trans('company_detail.heading')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>

      <li class="active"> {{trans('company_detail.heading')}}</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header">  
            <h3 class="box-title">{{trans('company_detail.title')}}</h3>     
          </div>
          <div class="box-body">
            <form method="POST" enctype="multipart/form-data" action="{{route('company.update')}}" accept-charset="UTF-8" >
              @csrf
                <div class="modal-body">
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('company_detail.company_logo')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('company_detail.company_logo')}}" name="company_logo" type="file">
                      <img src="{{asset($company_detail['company_logo']->value)}}" height="50" width="">
                    @error('company_logo')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div> 
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('company_detail.company_name')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('company_detail.company_name')}}" name="company_name" type="text" value="{{$company_detail['company_name']->value}}">
                    @error('company_name')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div>       
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('company_detail.GSTIN')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('company_detail.GSTIN')}}" name="GSTIN" type="text" value="{{$company_detail['GSTIN']->value}}">
                    @error('GSTIN')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('company_detail.contact_number')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('company_detail.contact_number')}}" name="contact_number" value="{{$company_detail['contact_number']->value}}">
                    @error('contact_number')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('company_detail.company_email')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('company_detail.company_email')}}" name="company_email" type="email" value="{{$company_detail['company_email']->value}}">
                     @error('company_email')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('company_detail.customer_care_email')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('company_detail.customer_care_email')}}" name="customer_care_email" type="email" value="{{$company_detail['customer_care_email']->value}}">
                     @error('customer_care_email')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('company_detail.company_address')}}
                    </label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('company_detail.company_address')}}" name="company_address" type="text" value="{{$company_detail['company_address']->value}}">
                     @error('company_address')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="country" class="content-label">{{trans('company_detail.country')}}</label>
                      <select class="form-control" name='country'>
                        @foreach($country as $con) 
                          <option value="{{$con->id}}"@if($company_detail['country']->value == $con->id) selected @endif>{{$con->country_name}}</option>
                        @endforeach 
                      </select>
                     @error('country')
                      <div class="help-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="modal-footer">
                  <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div> 
  </section>

  @endsection
           


