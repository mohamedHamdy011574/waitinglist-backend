@extends('layouts.admin')

@section('css')
<style>
  .details{padding: 10px; background: #efebeb; pointer-events: none;}
  .main_services {text-decoration: underline;}
  .main_services {text-decoration: underline; cursor: pointer; color: #337ab7; margin-left: 5px}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('subscription_packages.show') }}
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
        {{ trans('subscription_packages.show') }}
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
            <form method="POST" action="{{route('subscription_packages.update', $subscription_package->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              @csrf
              <input name="_method" type="hidden" value="PUT">
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
                            <p class="details">{{$subscription_package->translate($lk)->package_name}}
                            </p>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('subscription_packages.description')}}</label>
                            
                            <div class="details">{!! $subscription_package->translate($lk)->description !!}
                            </div>
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
                              <p class="details">
                                <input type="checkbox" id="for_restaurant" name="for_restaurant" value="1" 
                                @if($subscription_package->for_restaurant == 1) checked @endif >
                                <label for="for_restaurant" class="main_services"> {{trans('subscription_packages.restaurant')}}</label>
                              </p>

                              <div id="restaurant_services">
                                <p class="details">
                                  <input type="checkbox" id="reservation" name="reservation" value="1" 
                                  @if($subscription_package->reservation == 1) checked @endif >
                                  <label for="reservation"> {{trans('subscription_packages.reservation')}}</label>
                                  <br>
                                  <input type="checkbox" id="waiting_list" name="waiting_list" value="1" 
                                  @if($subscription_package->waiting_list == 1) checked @endif >
                                  <label for="waiting_list"> {{trans('subscription_packages.waiting_list')}}</label>
                                  <br>
                                  <input type="checkbox" id="pickup" name="pickup" value="1" 
                                  @if($subscription_package->pickup == 1) checked @endif >
                                  <label for="pickup"> {{trans('subscription_packages.pickup')}}</label>
                                </p>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <p class="details">
                                <input type="checkbox" id="for_catering" name="for_catering" value="1"
                                @if($subscription_package->for_catering == 1) checked @endif >
                                <label for="for_catering" class="main_services"> {{trans('subscription_packages.catering')}}</label>
                              </p>
                            </div>

                            <div class="col-md-4">
                              <p class="details">
                                <input type="checkbox" id="for_food_truck" name="for_food_truck" value="1"
                                @if($subscription_package->for_food_truck == 1) checked @endif>
                                <label for="for_food_truck" class="main_services"> {{trans('subscription_packages.food_truck')}}</label>
                              </p>
                            </div>
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="banners" class="content-label">{{trans('subscription_packages.branches_include')}}</label>
                          <p class="details">{{$subscription_package->branches_include}}
                          </p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="menus" class="content-label">{{trans('subscription_packages.subscription_period')}}</label>
                          <p class="details">{{$subscription_package->subscription_period}}
                          </p>
                        </div>
                        <div class="form-group">
                          <label for="menus" class="content-label">{{trans('subscription_packages.package_price')}}</label>
                          <p class="details">
                            {{$subscription_package->package_price}}
                          </p>
                        </div>
                        <div class="form-group">
                          <label for="menus" class="content-label">{{trans('subscription_packages.currency')}}</label>
                          <p class="details">
                            {{$subscription_package->currency}}
                          </p>
                        </div>
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <p class="details"> 
                            @if($subscription_package->status == 'active') {{trans('common.active')}}
                            @endif
                            @if($subscription_package->status == 'inactive') {{trans('common.inactive')}}
                            @endif
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                @can("subscription-package-edit")
                  <a class="btn btn-danger btn-fill btn-wd" href="{{route('subscription_packages.edit',$subscription_package->id)}}">
                    {{trans('common.edit')}}
                  </a>
                @endcan
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

    $('#package_for').change(function(){
      var package = $(this).val();
      var restaurant_services = "<option value='reservation' selected>{{trans('subscription_packages.reservation')}}</option><option value='waiting_list'>{{trans('subscription_packages.waiting_list')}}</option><option value='pickup'>{{trans('subscription_packages.pickup')}}</option>";
      var catering_services = "<option value='booking' selected>{{trans('subscription_packages.booking')}}</option>";
      var food_truck_services = "<option value='listing' selected>{{trans('subscription_packages.listing')}}</option>";
        
      $('#service_include').html('');
      $('#branches_include').removeAttr('readonly');
      
      if(package == 'restaurant'){
        $('#service_include').append(restaurant_services);
      }
      if(package == 'catering'){
        $('#service_include').append(catering_services);
        $('#branches_include').val(1);
        $('#branches_include').attr('readonly','readonly');
      }
      if(package == 'food_truck'){
        $('#service_include').append(food_truck_services);
      }
    })
</script>

<script>
  function langfileds(){
    return true;
    // var package_name = $('#package_name:en').value();
    // var description = $('#description:en').value();
  }
</script>
@endsection