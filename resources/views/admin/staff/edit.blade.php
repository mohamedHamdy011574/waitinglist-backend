@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{ trans('staff.show') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('staff.index')}}">{{trans('staff.singular')}}</a></li>
      <li class="active">{{ trans('staff.show') }} </li>
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
                <h3 class="box-title">{{ trans('staff.details') }}</h3>
                <ul class="pull-right">
                    <a href="{{route('staff.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>

            </div>
          <div class="box-body">
            <form method="POST" id="staff_form" action="{{route('staff.update', $staff->id)}}" accept-charset="UTF-8">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="model-body">
                <div class="row">
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('staff.first_name')}}</label>
                      <input class="form-control" placeholder="{{trans('staff.first_name')}}" required="true" name="first_name" type="text" id="first_name" value="{{$staff->first_name}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('staff.last_name')}}</label>
                      <input class="form-control" placeholder="{{trans('staff.last_name')}}" required="true" name="last_name" type="text" id="last_name" value="{{$staff->last_name}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('staff.email')}}</label>
                      <input class="form-control" placeholder="{{trans('staff.email')}}" required="true" name="email" type="text" id="email" value="{{$staff->email}}">
                    </div>
                  </div>
            
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('staff.phone_number')}}</label>
                      <input class="form-control" placeholder="{{trans('staff.phone_number')}}" required="true" name="phone_number" type="number" id="phone_number" value="{{$staff->phone_number}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('staff.password')}}</label>
                      <input class="form-control" placeholder="{{trans('staff.password')}}" name="password" type="password" id="password" value="{{old('password')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('staff.password_confirmation')}}</label>
                      <input class="form-control" placeholder="{{trans('staff.password_confirmation')}}"  name="password_confirmation" type="password" id="password_confirmation" value="{{old('password_confirmation')}}">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('staff.branch')}}</label>
                      {{$selected_business_branch_staff->id}}
                      <input type="hidden" name="business_branch_id" value="{{$selected_business_branch->id}}">
                      <select name="business_branch_id" id="business_branch_id" class="form-control select2" disabled   >
                        <option value="">{{trans('staff.select_branch')}}</option>
                        @foreach($business_branches as $b_branch)
                        <option value="{{$b_branch->id}}" reservation_allow="{{$b_branch->reservation_allow}}" waiting_list_allow="{{$b_branch->waiting_list_allow}}" pickup_allow="{{$b_branch->pickup_allow}}" @if($b_branch->branch_type == 'catering') catering_allow="1" @else catering_allow="0" @endif
                          {{ ($selected_business_branch_staff->business_branch_id == $b_branch->id) ? 'selected':'' }} 
                        >{{$b_branch->branch_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group" id="choose_services">
                      <label for="name"> {{trans('staff.manages')}}</label>
                      
                      @if($selected_business_branch->reservation_allow)
                      <div id="manage_reservations">
                        <input type="checkbox" id="manage_reservations_cb" name="manage_reservations" value="1">
                        <label for="manage_reservations_cb" class="main_services"> {{trans('staff.mng_reservations')}}</label><br>
                      </div>
                      @endif

                      @if($selected_business_branch->waiting_list_allow)
                      <div id="manage_waiting_list">
                        <input type="checkbox" id="manage_waiting_list_cb" name="manage_waiting_list" value="1">
                        <label for="manage_waiting_list_cb" class="main_services"> {{trans('staff.mng_waiting_list')}}</label><br>
                      </div>
                      @endif

                      @if($selected_business_branch->pickup_allow)
                      <div id="manage_pickups">
                        <input type="checkbox" id="manage_pickups_cb" name="manage_pickups" value="1">
                        <label for="manage_pickups_cb" class="main_services"> {{trans('staff.mng_pickup_orders')}}</label><br>
                      </div> 
                      @endif

                      @if($selected_business_branch->branch_type == 'catering')
                      <div id="manage_catering_bookings">
                        <input type="checkbox" id="manage_catering_cb" name="manage_catering_bookings" value="1">
                        <label for="manage_catering_cb" class="main_services"> {{trans('staff.mng_catering_orders')}}</label><br>
                      </div> 
                      @endif

                    </div>
                  </div>
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

<script>
    //Initialize Select2 Elements
    $('.select2').select2();

    CKEDITOR.replaceAll();
    // $('#manage_reservations,#manage_waiting_list,#manage_pickups').hide();

    function addCheckbox(name) {
       $('#'+name).show();
    }

    $('#manage_reservations,#manage_waiting_list,#manage_pickups,#manage_catering_bookings').hide();
    @if($selected_business_branch->reservation_allow)
      $('#manage_reservations').show();
      @if($selected_business_branch_staff->manage_reservations)
        $('#manage_reservations_cb').prop('checked',true)
      @endif
    @endif

    @if($selected_business_branch->waiting_list_allow)
      $('#manage_waiting_list').show();
      @if($selected_business_branch_staff->manage_waiting_list)
        $('#manage_waiting_list_cb').prop('checked',true)
      @endif
    @endif

    @if($selected_business_branch->pickup_allow)
      $('#manage_pickups').show();
      @if($selected_business_branch_staff->manage_pickups)
        $('#manage_pickups_cb').prop('checked',true)
      @endif
    @endif
    @if($selected_business_branch->branch_type == 'catering')
      $('#manage_catering_bookings').show();
      @if($selected_business_branch_staff->manage_catering_bookings)
        $('#manage_catering_cb').prop('checked',true)
      @endif
    @endif

    $('#business_branch_id').change(function() { 
      var reservation_allow = $('option:selected', this).attr('reservation_allow');
      var waiting_list_allow = $('option:selected', this).attr('waiting_list_allow');
      var pickup_allow = $('option:selected', this).attr('pickup_allow');
      var catering_allow = $('option:selected', this).attr('catering_allow');
      
      $('#manage_reservations,#manage_waiting_list,#manage_pickups,#manage_catering_bookings').hide();
      $('#manage_reservations_cb,#manage_waiting_list_cb,#manage_pickups_cb,#manage_catering_cb').prop('checked',false);

      if(reservation_allow == 1) {
        $('#manage_reservations').show();
      }
      if(waiting_list_allow == 1) {
        $('#manage_waiting_list').show();
      }
      if(pickup_allow == 1) {
        $('#manage_pickups').show();
      }
      if(catering_allow == 1) {
        $('#manage_catering_bookings').show();
      }
    });


</script>

@endsection