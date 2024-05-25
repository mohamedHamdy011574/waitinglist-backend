@extends('layouts.admin')

@section('css')
<style>
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
  .main_services {text-decoration: underline; cursor: pointer; color: #337ab7; margin-left: 5px}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('pickup_hours.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('restaurants.index')}}">{{trans('pickup_hours.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('pickup_hours.edit') }}
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
            <h3 class="box-title">{{ trans('pickup_hours.details') }}</h3>
            @can('pickup-hours-list')
            <ul class="pull-right">
                <a href="{{route('pickup_hours.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="pkp_hour_form" action="{{route('pickup_hours.update', $pickup_hour->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="shift_name:{{$lk}}" class="content-label">{{trans('pickup_hours.shift_name')}}</label>
                            <input class="form-control"  placeholder="{{trans('pickup_hours.shift_name')}}" name="shift_name:{{$lk}}" id="shift_name:{{$lk}}" type="text" value="{{$pickup_hour->translate($lk)->shift_name}}" 
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
                          <label for="status" class="content-label">{{trans('pickup_hours.pickup_slot_duration')}}</label>
                          <input class="form-control"  placeholder="{{trans('pickup_hours.pickup_slot_duration')}}" name="pickup_slot_duration" id="pickup_slot_duration" type="number" min="1" value="{{$pickup_hour->pickup_slot_duration}}" required >
                          <strong class="help-block" id="pickup_slot_duration_error">
                            {{ @$errors->first('pickup_slot_duration') }}
                          </strong>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('pickup_hours.from_time')}}</label>
                          <input class="form-control"  placeholder="{{trans('pickup_hours.from_time')}}" name="from_time" id="from_time" type="text" value="{{$pickup_hour->from_time}}" required >
                              <strong class="help-block">
                                {{ @$errors->first('from_time') }}
                              </strong>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('pickup_hours.to_time')}}</label>
                          <input class="form-control"  placeholder="{{trans('pickup_hours.to_time')}}" name="to_time" id="to_time" type="text" value="{{$pickup_hour->to_time}}" required >
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
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <select class="form-control" name="status" id="status" required>
                            <option value="active" 
                              @if($pickup_hour->status == 'active') selected @endif>
                              {{trans('common.active')}}
                            </option>
                            <option value="inactive" 
                              @if($pickup_hour->status == 'inactive') selected @endif>
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
                <button id="pkp_hrs_btn" onclick="return langfileds();" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('js')
<script>
  function langfileds(){
    return true;
  }
  $('#from_time').datetimepicker({
    format: 'hh:mm A',
    stepping: 15
  });
  $('#to_time').datetimepicker({
    format: 'hh:mm A',
    stepping: 15
  });
  //validation for dining slot duration
  $(document).on('click','#pkp_hrs_btn',function(e){
    if(parseInt($('#pickup_slot_duration').val()) != 0){
      e.preventDefault();

      var pickup_slot_duration = parseInt($('#pickup_slot_duration').val());

      var from_time = $('#from_time').val();
      var to_time = $('#to_time').val();
      var startTime = moment(from_time, "HH:mm:ss a");
      var endTime = moment(to_time, "HH:mm:ss a");
      var hrs = endTime.diff(startTime, 'hours');
      var shift_mins_diff = parseInt(hrs * 60) + parseInt(moment.utc(moment(endTime, "HH:mm:ss").diff(moment(startTime, "HH:mm:ss"))).format("mm"));

      if(shift_mins_diff >= pickup_slot_duration)
      {
        $('#pkp_hour_form').submit();
      }
      else
      {
        $("#pickup_slot_duration_error").text("{{trans('pickup_hours.pickup_slot_duration_error')}}");
      }

    } else {
        $("#pickup_slot_duration_error").text("{{trans('pickup_hours.pickup_slot_duration_error_not_zero')}}");
      
    }
  });
</script>
@endsection