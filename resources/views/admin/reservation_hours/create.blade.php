@extends('layouts.admin')

@section('css')
<style>
  .main_services {text-decoration: underline; cursor: pointer; color: #337ab7; margin-left: 5px}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('reservation_hours.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('reservation_hours.index')}}">{{trans('reservation_hours.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('reservation_hours.add_new') }}
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
            <h3 class="box-title">{{ trans('reservation_hours.details') }}</h3>
            @can('reservation-hours-list')
            <ul class="pull-right">
                <a href="{{route('reservation_hours.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="res_hour_form" action="{{route('reservation_hours.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="shift_name:{{$lk}}" class="content-label">{{trans('reservation_hours.shift_name')}}</label>
                            <input class="form-control"  placeholder="{{trans('reservation_hours.shift_name')}}" name="shift_name:{{$lk}}" id="shift_name:{{$lk}}" type="text" value="{{old('shift_name:'.$lk)}}" 
                              @if($lk=='en') required @endif >
                              <strong class="help-block">
                                {{ @$errors->first('shift_name:'.$lk) }}
                              </strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">                      
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reservation_hours.dining_slot_duration')}}</label>
                          <input class="form-control"  placeholder="{{trans('reservation_hours.dining_slot_duration')}}" name="dining_slot_duration" id="dining_slot_duration" value="{{old('dining_slot_duration')}}" type="number" min="1" required >
                          <strong class="help-block" id="dining_slot_duration_error">
                            {{ @$errors->first('dining_slot_duration') }}
                          </strong>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reservation_hours.from_time')}}</label>
                          <input class="form-control"  placeholder="{{trans('reservation_hours.from_time')}}" name="from_time" id="from_time" type="text" value="{{old('from_time')}}" required >
                              <strong class="help-block">
                                {{ @$errors->first('from_time') }}
                              </strong>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reservation_hours.to_time')}}</label>
                          <input class="form-control"  placeholder="{{trans('reservation_hours.to_time')}}" name="to_time" id="to_time" type="text" value="{{old('to_time')}}" required >
                              <strong class="help-block">
                                {{ @$errors->first('to_time') }}
                              </strong>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reservation_hours.allowed_chair')}}</label>
                          <input class="form-control"  placeholder="{{trans('reservation_hours.allowed_chair')}}" name="allowed_chair" id="allowed_chair" type="number" value="{{old('allowed_chair')}}" required >
                              <strong class="help-block">
                                {{ @$errors->first('allowed_chair') }}
                              </strong>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
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
                <button id="res_hrs_btn" onclick="return langfileds();" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
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
    $('#from_time').datetimepicker({
      format: 'hh:mm A',
      stepping: 15
    });
    $('#to_time').datetimepicker({
      format: 'hh:mm A',
      stepping: 15
    });
</script>

<script>
  function langfileds(){
    return true;
    // var package_name = $('#package_name:en').value();
    // var description = $('#description:en').value();
  }

  //validation for dining slot duration
  $(document).on('click','#res_hrs_btn',function(e){
    if((parseInt($('#dining_slot_duration').val())) > 0){
      e.preventDefault();

      var dining_slot_duration = parseInt($('#dining_slot_duration').val());

      var from_time = $('#from_time').val();
      var to_time = $('#to_time').val();
      var startTime = moment(from_time, "HH:mm:ss a");
      var endTime = moment(to_time, "HH:mm:ss a");
      var hrs = endTime.diff(startTime, 'hours');
      var shift_mins_diff = parseInt(hrs * 60) + parseInt(moment.utc(moment(endTime, "HH:mm:ss").diff(moment(startTime, "HH:mm:ss"))).format("mm"));

      if(shift_mins_diff >= dining_slot_duration){
        $('#res_hour_form').submit();
      } else {
        $("#dining_slot_duration_error").text("{{trans('reservation_hours.dining_slot_duration_error')}}");
      }
    } else {
      $("#dining_slot_duration_error").text("{{trans('reservation_hours.dining_slot_duration_error_not_zero')}}");
    }
  });
</script>
@endsection