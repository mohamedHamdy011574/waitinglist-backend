@extends('layouts.admin')

@section('css')
<style>
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }

  .weekDays-selector input {
    position: absolute;
    visibility: hidden;
    /*display: none!important;*/
  }

  .weekDays-selector input[type=checkbox] + label {
    display: inline-block;
    border-radius: 6px;
    background: #dddddd;
    height: 40px;
    width: 40px;
    margin-right: 3px;
    line-height: 40px;
    text-align: center;
    cursor: pointer;
  }

  .weekDays-selector input[type=checkbox]:checked + label {
    background: green;
    color: #ffffff;
  }
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('businesses.details') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      @can('businesses')
      <li>
        <a href="{{route('businesses.index')}}">{{trans('businesses.singular')}}</a>
      </li>
      @endcan
      <li class="active">
        {{ trans('businesses.details') }}
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
            <h3 class="box-title">{{ trans('businesses.details') }}</h3>
            @can('restaurant-list')
            <ul class="pull-right">
                <a href="{{route('businesses.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('businesses.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="brand_name:{{$lk}}" class="content-label">{{trans('businesses.brand_name')}}</label>
                            <input class="form-control"  placeholder="{{trans('businesses.brand_name')}}" name="brand_name:{{$lk}}" id="brand_name:{{$lk}}" type="text" value="{{old('brand_name:'.$lk)}}" 
                            @if($lk=='en') required @endif >
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('businesses.description')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('businesses.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" @if($lk=='en') required @endif >{{old('description:'.$lk)}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="brand_email" class="content-label">{{trans('businesses.brand_email')}}</label>
                      <input class="form-control" placeholder="{{trans('businesses.brand_email')}}" type="email" name="brand_email" value="{{old('brand_email')}}" id="brand_email" required>
                    </div>
                    <div class="form-group">
                      <label for="brand_" class="content-label">{{trans('businesses.brand_phone_number')}}</label>
                      <input class="form-control" placeholder="{{trans('businesses.brand_phone_number')}}" type="text" name="brand_phone_number" value="{{old('brand_phone_number')}}" id="brand_phone_number" required>
                    </div>
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('businesses.link')}}</label>
                      <input class="form-control" placeholder="{{trans('businesses.link')}}" type="url" name="link" value="{{old('link')}}" id="link" required>
                    </div>
                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('businesses.cuisine')}}
                      </label>
                      <select class="form-control multiselect1" id="cuisines" name="cuisines[]" multiple data-placeholder="{{trans('businesses.select_cuisines')}}" required>
                        @foreach($cuisines as $cuisine)
                        <option value="{{$cuisine->id}}"
                          {{ (collect(old('cuisines'))->contains($cuisine->id)) ? 'selected':'' }}
                          >{{$cuisine->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('businesses.banners')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="banners[]" id="banners" required>
                    </div>
                    <!-- <div class="form-group">
                      <label for="brand_logo" class="content-label">{{trans('businesses.brand_logo')}}</label>
                      <input class="form-control" type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="brand_logo" id="brand_logo" required>
                    </div> -->
                  </div>
                  <div class="col-md-6">  
                    <div class="form-group">
                      <label for="working_status" class="content-label">{{trans('businesses.working_status')}}</label>
                      <select class="form-control" name="working_status" id="working_status" required>
                        <option value="available" 
                          @if(old('working_status') == 'available') selected @endif>
                          {{trans('businesses.available')}}
                        </option>
                        <option value="busy" 
                          @if(old('working_status') == 'busy') selected @endif>
                          {{trans('businesses.busy')}}
                        </option>
                        <option value="closed" 
                          @if(old('working_status') == 'closed') selected @endif>
                          {{trans('businesses.closed')}}
                        </option>
                        <option value="orders_suspended" 
                          @if(old('working_status') == 'orders_suspended') selected @endif>
                          {{trans('businesses.orders_suspended')}}
                        </option>
                      </select>
                    </div>

                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('businesses.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="banners" class="content-label">{{trans('catering_plans.serving_days_time')}}</label>
                            <div class="weekDays-selector">
                                <input type="checkbox" name="sunday_serving" id="sunday_serving" class="weekday" value="1" 
                                @if((old('sunday_serving')==1)) checked @endif />
                                <label for="sunday_serving">{{trans('catering_plans.week_days.0')}}</label>

                                <input type="checkbox" name="monday_serving" id="monday_serving" class="weekday" value="1"
                                @if((old('monday_serving')==1)) checked @endif />
                                <label for="monday_serving">{{trans('catering_plans.week_days.1')}}</label>

                                <input type="checkbox" name="tuesday_serving" id="tuesday_serving" class="weekday" value="1" 
                                @if((old('tuesday_serving')==1)) checked @endif/>
                                <label for="tuesday_serving">{{trans('catering_plans.week_days.2')}}</label>

                                <input type="checkbox" name="wednesday_serving" id="wednesday_serving" class="weekday" value="1"
                                @if((old('wednesday_serving')==1)) checked @endif/>
                                <label for="wednesday_serving">{{trans('catering_plans.week_days.3')}}</label>

                                <input type="checkbox" name="thursday_serving" id="thursday_serving" class="weekday" value="1"
                                @if((old('thursday_serving')==1)) checked @endif/>
                                <label for="thursday_serving">{{trans('catering_plans.week_days.4')}}</label>

                                <input type="checkbox" name="friday_serving" id="friday_serving" class="weekday" value="1"
                                @if((old('friday_serving')==1)) checked @endif/>
                                <label for="friday_serving">{{trans('catering_plans.week_days.5')}}</label>

                                <input type="checkbox" name="saturday_serving" id="saturday_serving" class="weekday" value="1"
                                @if((old('saturday_serving')==1)) checked @endif/>
                                <label for="saturday_serving">{{trans('catering_plans.week_days.6')}}</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <!-- <label for="from_day" class="content-label">{{trans('businesses.day_from')}}</label>
                          <select class="form-control" name="from_day" id="from_day" required>
                            @foreach([0,1,2,3,4,5,6] as $day)
                            <option value="{{$day}}" @if($day == old('from_day')) selected @endif>{{trans('businesses.working_hours.'.$day)}}
                            </option>
                            @endforeach
                          </select>  -->
                          <label for="status" class="content-label">{{trans('businesses.time_from')}}</label>
                          <input name="from_time" required class="form-control" type="text" id="from_time" value="{{old('from_time')}}">   
                        </div>
                        <div class="col-md-6">
                          <!-- <label for="status" class="content-label">{{trans('businesses.day_to')}}</label>
                          <select class="form-control" name="to_day" id="status" required>
                            @foreach([0,1,2,3,4,5,6] as $day2)
                            <option value="{{$day2}}" @if($day2 == old('to_day')) selected @endif>{{trans('businesses.working_hours.'.$day2)}}
                            </option>
                            @endforeach
                          </select> -->
                          <label for="status" class="content-label">{{trans('businesses.time_to')}}</label>
                          <input name="to_time" required class="form-control" type="text" id="to_time" value="{{old('to_time')}}">
                        </div>
                      </div>

                    </div>
                </div>
              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('Submit')}}</button>
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
    $('#cuisines_input').attr('autocomplete','off');
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
@endsection