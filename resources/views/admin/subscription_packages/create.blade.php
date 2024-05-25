@extends('layouts.admin')

@section('css')
<style>
  .main_services {text-decoration: underline; cursor: pointer; color: #337ab7; margin-left: 5px}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('subscription_packages.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('subscription_packages.index')}}">{{trans('subscription_packages.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('subscription_packages.add_new') }}
      </li>
    </ol>
  </section>
  <section class="content">
    @if ($errors->any())
    <div class="alert alert-danger">
      <b>{{trans('common.whoops')}}</b>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{ trans('subscription_packages.details') }}</h3>
            @can('restaurant-list')
            <ul class="pull-right">
                <a href="{{route('subscription_packages.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('subscription_packages.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
              @csrf
              <div class="model-body">
                <div class="row">
                  <div class="col-md-12">
                    <ul class="nav nav-tabs" role="tablist">
                      @foreach(config('app.locales') as $lk=>$lv)
                        <li role="presentation" class="@if($lk=='en') active @endif">
                          <a href="#abc_{{$lk}}" aria-controls="" role="tab" data-toggle="tab" aria-expanded="true">
                                    {{$lv['name']}}
                          </a>
                        </li>  
                      @endforeach
                    </ul>
                    <div class="tab-content" style="margin-top: 10px;">
                      @foreach(config('app.locales') as $lk=>$lv)
                        <div role="tabpanel" class="tab-pane @if($lk=='en') active @endif" id="abc_{{$lk}}">
                          <div class="form-group">
                            <label for="package_name:{{$lk}}" class="content-label">{{trans('subscription_packages.package_name')}}</label>
                            <input class="form-control"  placeholder="{{trans('subscription_packages.package_name')}}" name="package_name:{{$lk}}" id="package_name:{{$lk}}" type="text" value="{{old('package_name:'.$lk)}}" 
                              @if($lk=='en') required @endif >
                              <strong class="help-block">
                                {{ @$errors->first('package_name:'.$lk) }}
                              </strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('subscription_packages.description')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('subscription_packages.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" 
                              @if($lk=='en') required @endif>{{old('description:'.$lk)}}</textarea>
                              <strong class="help-block">
                                  {{ @$errors->first('description:'.$lk) }}
                              </strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="content-label">{{trans('subscription_packages.package_for')}}</label>
                          <div class="row">
                            <div class="col-md-4">
                              <input type="checkbox" id="for_restaurant" name="for_restaurant" value="1" 
                              @if(old('for_restaurant')) checked @endif >
                              <label for="for_restaurant" class="main_services"> {{trans('subscription_packages.restaurant')}}</label><br>

                              <div id="restaurant_services">
                                <input type="checkbox" id="reservation" name="reservation" value="1" 
                                @if(old('reservation')) checked @endif >
                                <label for="reservation"> {{trans('subscription_packages.reservation')}}</label>
                                <br>
                                <input type="checkbox" id="waiting_list" name="waiting_list" value="1" 
                                @if(old('waiting_list')) checked @endif >
                                <label for="waiting_list"> {{trans('subscription_packages.waiting_list')}}</label>
                                <br>
                                <input type="checkbox" id="pickup" name="pickup" value="1" 
                                @if(old('pickup')) checked @endif >
                                <label for="pickup"> {{trans('subscription_packages.pickup')}}</label><br>
                              </div>
                            </div>
                          

                            <div class="col-md-4">
                              <input type="checkbox" id="for_catering" name="for_catering" value="1"
                              @if(old('for_catering')) checked @endif >
                              <label for="for_catering" class="main_services"> {{trans('subscription_packages.catering')}}</label>
                            </div>

                            <div class="col-md-4">
                              <input type="checkbox" id="for_food_truck" name="for_food_truck" value="1"
                              @if(old('for_food_truck')) checked @endif>
                              <label for="for_food_truck" class="main_services"> {{trans('subscription_packages.food_truck')}}</label>
                            </div>
                          </div>

                        </div>
                        <div class="form-group">
                          <label for="banners" class="content-label">{{trans('subscription_packages.branches_include')}}</label>
                          <input class="form-control" type="number" name="branches_include" id="branches_include" min="1" max="20" value="{{old('branches_include')}}" required>
                          <strong class="help-block">
                            {{ @$errors->first('branches_include') }}
                          </strong>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="menus" class="content-label">{{trans('subscription_packages.subscription_period')}}</label>
                          <input class="form-control" type="number" name="subscription_period" min="1" max="20" value="{{old('subscription_period')}}"  required>
                          <strong class="help-block">
                            {{ @$errors->first('subscription_period') }}
                          </strong>
                        </div>
                        <div class="form-group">
                          <label for="menus" class="content-label">{{trans('subscription_packages.package_price')}}</label>
                          <input class="form-control" type="number" name="package_price" min="1" value="{{old('package_price')}}"  required>
                          <strong class="help-block">
                            {{ @$errors->first('package_price') }}
                          </strong>
                        </div>
                        <div class="form-group">
                          <label for="menus" class="content-label">{{trans('subscription_packages.currency')}}</label>
                          <input class="form-control" type="text" name="currency" value="KD" readonly required>
                        </div>
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <select class="form-control" name="status" id="status" required>
                            <option value="active" 
                              @if(old('status') == 'active') selected @endif>
                              {{trans('common.active')}}
                            </option>
                            <option value="inactive" 
                              @if(old('status') == 'inactive') selected @endif>
                              {{trans('common.inactive')}}
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button id="submit Button" onclick="return langfileds();" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
<!-- <script src="{{ asset('admin/bower_components/ckeditor/ckeditor.js') }}"></script> -->
@section('js')
<script>
    CKEDITOR.replaceAll();
</script>

<script>
    //Initialize Select2 Elements
    $('.select2').select2();
    @if(old('for_restaurant'))
      $('#restaurant_services').show();
    @else
      $('#restaurant_services').hide();
    @endif
    $("#for_restaurant").change(function() {
      if(this.checked) {
        $('#restaurant_services').show();
      }else{
        $('#restaurant_services').hide();
      }
    });
    
</script>

<script>
  function langfileds(){
    return true;
    // var package_name = $('#package_name:en').value();
    // var description = $('#description:en').value();
  }
</script>
@endsection