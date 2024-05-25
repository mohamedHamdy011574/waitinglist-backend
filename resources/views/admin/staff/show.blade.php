@extends('layouts.admin')

@section('css')
  <style type="text/css">
    .details{padding: 10px; background: #efebeb}
    #choose_services {pointer-events: none;}
  </style>
@endsection


@section('content')
  <section class="content-header">
    <h1>
      {{ trans('staff.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('staff.index')}}">{{trans('staff.singular')}}</a></li>
      <li class="active">{{ trans('staff.edit') }} </li>
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
                      <label for="name"> {{trans('managers.first_name')}}</label>
                      <p class="details">{{$staff->first_name}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.last_name')}}</label>
                      <p class="details">{{$staff->last_name}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.email')}}</label>
                      <p class="details">{{$staff->email}}</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('managers.phone_number')}}</label>
                      <p class="details">{{$staff->phone_number}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.password')}}</label>
                      <p class="details">********</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.password_confirmation')}}</label>
                      <p class="details">********</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('staff.branch')}}</label>
                      <p class="details">
                      
                        @foreach($business_branches as $b_branch)
                        
                          {{ ($selected_business_branch_staff->business_branch_id == $b_branch->id) ? $b_branch->branch_name  :'' }} 
                        
                        @endforeach
                      
                    </div>
                    <div class="form-group" id="choose_services">
                      <label for="name"> {{trans('staff.manages')}}</label>

                      @if($selected_business_branch->reservation_allow)
                      <div id="manage_reservations">
                        <input type="checkbox" id="manage_reservations_cb" name="manage_reservations" value="1">
                        <label for="manage_reservations_cb" class="main_services"> {{trans('staff.mng_reservations')}} </label><br>
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

                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('staff.edit', $staff->id)}}">{{trans('common.edit')}}</a>
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

    $('#manage_reservations,#manage_waiting_list,#manage_pickups').hide();
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

    $('#business_branch_id').change(function() { 
      var reservation_allow = $('option:selected', this).attr('reservation_allow');
      var waiting_list_allow = $('option:selected', this).attr('waiting_list_allow');
      var pickup_allow = $('option:selected', this).attr('pickup_allow');
      
      $('#manage_reservations,#manage_waiting_list,#manage_pickups').hide();
      $('#manage_reservations_cb,#manage_waiting_list_cb,#manage_pickups_cb').prop('checked',false);

      if(reservation_allow == 1) {
        $('#manage_reservations').show();
      }
      if(waiting_list_allow == 1) {
        $('#manage_waiting_list').show();
      }
      if(pickup_allow == 1) {
        $('#manage_pickups').show();
      }
    });


</script>

@endsection