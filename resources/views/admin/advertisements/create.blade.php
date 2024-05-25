@extends('layouts.admin')

@section('css')
<style>
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
  .food_servings{margin-bottom: 10px}
  #add_food_serving {margin-left: 5px;}
  .person_to_served{width: 70px; display: inline;}
  .person_to_served_seprator{padding: 0px 10px; display: inline;}
  .service_type {margin-right: 5px !important;}

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

  input.off_prem_times {width: 50%; display: inline;}
  select.off_prem_times {width: 40%; display: inline;}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('advertisements.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('advertisements.index')}}">{{trans('advertisements.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('advertisements.add_new') }}
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
            <h3 class="box-title">{{ trans('advertisements.details') }}</h3>
            @can('catering-plan-list')
            <ul class="pull-right">
                <a href="{{route('advertisements.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('advertisements.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('advertisements.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('advertisements.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{old('name:'.$lk)}}" 
                            @if($lk=='en') required @endif>
                            <strong class="help-block">
                              {{ @$errors->first('name:'.$lk) }}
                            </strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('advertisements.video')}}</label>
                      <input class="form-control" type="file" accept=".flv,.mp4,.m3u8,.ts,.3gp,.mov,.avi,.wmv" name="video" id="menu_item_photo" required>
                      <strong class="help-block">
                        {{ @$errors->first('video') }}
                      </strong>
                    </div>
                  </div>

                      <div class="col-md-6">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="duration_from" class="content-label">{{trans('advertisements.duration_from')}}</label>
                              <input type="text" class="form-control pull-right" name="duration_from" autocomplete="off" id="duration_from" value="{{old('duration_from')}}">
                              <strong class="help-block">
                                {{ @$errors->first('duration_from') }}
                              </strong>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="duration_to" class="content-label">{{trans('advertisements.duration_to')}}</label>
                              <input name="duration_to" id="duration_to" required class="form-control" readonly type="text" value="{{old('duration_to')}}">
                              <strong class="help-block">
                                {{ @$errors->first('duration_to') }}
                              </strong>
                            </div>
                          </div>
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
              <div class="modal-footer">
                <button id="edit_btn" type="submit" onclick="return checkfiles();" class="btn btn-info btn-fill btn-wd">{{trans('Submit')}}</button>
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


<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{ asset('admin/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<script src="{{ asset('admin/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

<script>
  
    CKEDITOR.replaceAll();

    //upload files limit
    function checkfiles() {
      var $fileUpload = $("input[type='file']");
      if (parseInt($fileUpload.get(0).files.length) > 5){
        alert("You are only allowed to upload a maximum of 5 files");
          return false;
      }
    }

    //Date picker
    $('#duration_from').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
    }).change(function(){
      var duration_to = $(this).val();

      $('#duration_to').val(moment(moment(duration_to)).add(1, 'M').format('YYYY-MM-DD'));
    })
</script>

<!-- <script>
    //Initialize Select2 Elements
    $('#add_food_serving').click(function(){
      var food_serving_input = '<input name="food_serving[]" required class="form-control food_servings" type="text">';
      $('#food_serving_en').append(food_serving_input);
    });
</script> -->
@endsection