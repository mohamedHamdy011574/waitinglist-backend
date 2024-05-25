@extends('layouts.admin')
@section('css')
  <style>
    div.packages{padding: 10px; background: #fbfbfb;}
    div.packages table{cursor: pointer;}
    div.packages:hover{background: #e5e5e5}
    .package_type{background: #ecf0f5; width: 100%; margin-bottom: 10px;}
    .package_type h4{font-weight: 600}
    .deselect{font-weight: bold; margin:4px; padding: 5px 10px; background: #333; color:#fff; cursor: pointer;}
    .table{margin-bottom: 0px}
    ul.main_services{padding-left: 10px; list-style-type: square;}
    ul.main_services > li{ margin-bottom:10px;}
    ul.inner_services{padding-left: 15px}
  </style>
@endsection
@section('content')
  <section class="content-header">
    <h1>
      {{ trans('vendors.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('vendors.index')}}">{{trans('vendors.singular')}}</a></li>
      <li class="active">{{ trans('vendors.add_new') }} </li>
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
                <h3 class="box-title">{{ trans('vendors.details') }}</h3>
                <ul class="pull-right">
                    <a href="{{route('vendors.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
            </div>
          <div class="box-body">
            <form method="POST" id="vendors_form" action="{{route('vendors.store')}}" accept-charset="UTF-8">
              @csrf
              <div class="model-body">
                <div class="row">
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('vendors.first_name')}}</label>
                      <input class="form-control" placeholder="{{trans('vendors.first_name')}}" required="true" name="first_name" type="text" id="first_name" value="{{old('first_name')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('vendors.last_name')}}</label>
                      <input class="form-control" placeholder="{{trans('vendors.last_name')}}" required="true" name="last_name" type="text" id="last_name" value="{{old('last_name')}}">
                    </div>
                    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('vendors.email')}}</label>
                      <input class="form-control" placeholder="{{trans('vendors.email')}}" required="true" name="email" type="text" id="email" value="{{old('email')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('vendors.phone_number')}}</label>
                      <input class="form-control" placeholder="{{trans('vendors.phone_number')}}" required="true" name="phone_number" type="number" min="0" id="phone_number" value="{{old('phone_number')}}">
                    </div>
                    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('vendors.password')}}</label>
                      <input class="form-control" placeholder="{{trans('vendors.password')}}" required="true" name="password" type="password" id="password" value="{{old('password')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('vendors.password_confirmation')}}</label>
                      <input class="form-control" placeholder="{{trans('vendors.password_confirmation')}}" required="true" name="password_confirmation" type="password" id="password_confirmation" value="{{old('password_confirmation')}}">
                    </div>
                  </div>
                </div>

                <h3 class="box-title">{{trans('vendors.choose_subscription')}}</h3>
                <!--  Packages -->
                <div class="row">
                  
                  @foreach($subscription_packages as $rpckg)
                  <div class="col-md-4">
                    <div class="form-group packages">
                      <div>
                        <input type="radio" class="sub_packages" id="subscription_package_id{{$rpckg->id}}"  name="subscription_package" value="{{$rpckg->id}}" @if(old('subscription_package') == $rpckg->id) checked @endif>
                        <br/>
                        <label for="subscription_package_id{{$rpckg->id}}">{{$rpckg->name}}
                          <table class="table">
                            <tr>
                              <td>{{trans('subscription_packages.package_name')}}</td>
                              <td> : </td>
                              <td>{{$rpckg->package_name}}</td>
                            </tr>
                            <tr>
                              <td>
                                  {{trans('subscription_packages.included_services')}}
                              </td>
                              <td> : </td>
                              <td>
                                <ul class="main_services">
                                @if($rpckg->for_restaurant)
                                <li>
                                {{ trans('subscription_packages.restaurant') }}
                                <ul class="inner_services">
                                @if($rpckg->reservation)
                                  <li>
                                    {{ trans('subscription_packages.reservation') }}
                                  </li>  
                                @endif
                                @if($rpckg->waiting_list)
                                <li>
                                  {{ trans('subscription_packages.waiting_list') }}  
                                </li>
                                @endif
                                @if($rpckg->pickup)
                                <li>
                                  {{ trans('subscription_packages.pickup') }}  
                                </li>
                                @endif
                                </ul>
                                </li>
                                @endif

                                @if($rpckg->for_catering)
                                <li>
                                {{ trans('subscription_packages.catering') }}
                                </li>
                                @endif

                                @if($rpckg->for_food_truck)
                                <li>
                                {{ trans('subscription_packages.food_truck') }}
                                </li>
                                @endif
                              </ul>
                              </td>
                            </tr>
                            <tr>
                              <td>{{trans('subscription_packages.branches_include')}}</td>
                              <td> : </td>
                              <td>{{$rpckg->branches_include}}</td>
                            </tr>
                            <tr>
                              <td>{{trans('subscription_packages.description')}}</td>
                              <td> : </td>
                              <td>{!!$rpckg->description!!}</td>
                            </tr>
                            <tr>
                              <td>{{trans('subscription_packages.subscription_period')}}</td>
                              <td> : </td>
                              <td>{{$rpckg->subscription_period}}</td>
                            </tr>
                            <tr>
                              <td>{{trans('subscription_packages.package_price')}}</td>
                              <td> : </td>
                              <td>{{$rpckg->package_price}} {{$rpckg->currency}}</td>
                            </tr>
                          </table>
                        </label>
                      </div>
                    </div>
                  </div>
                  @endforeach
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
<!-- <script src="{{ asset('admin/bower_components/ckeditor/ckeditor.js') }}"></script> -->
@section('js')
<script type="text/javascript">
  $('#deselect_restaurant').click(function(){
    $("input[type='radio'][name='restaurant_package']").prop('checked', false);
  });
  $('#deselect_catering').click(function(){
    $("input[type='radio'][name='catering_package']").prop('checked', false);
  });
  $('#deselect_food_truck').click(function(){
    $("input[type='radio'][name='food_truck_package']").prop('checked', false);
  });
</script>
@endsection