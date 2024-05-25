@extends('layouts.admin')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .food_truck_media {display: inline-flex; margin-top: 10px}
    .food_truck_media img{width: 100px; height: 100px; padding: 4px; margin:6px 1px 2px 0px; background: #e5e5e5; border-radius: 6px; } 
    .food_truck_media i{font-size: 15px; color: #c10707; cursor: pointer}
    .loading{visibility: hidden; font-size:10px;}
    .loading i{color: green;}
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
  </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('food_trucks.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('food_trucks.index')}}">{{trans('food_trucks.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('food_trucks.edit') }}
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
            <h3 class="box-title">{{ trans('food_trucks.details') }}</h3>
            @can('food_truck-list')
            <ul class="pull-right">
                <a href="{{route('food_trucks.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('food_trucks.update', $food_truck->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="model-body">
                <div class="row">
                  <div class="col-md-6">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('food_trucks.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('food_trucks.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{$food_truck->translate($lk)->name}}">
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('food_trucks.description')}}</label>
                            <textarea class="form-control" rows="5" placeholder="{{trans('food_trucks.description')}}" name="description:{{$lk}}" id="description:{{$lk}}">{{$food_truck->translate($lk)->description}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('food_trucks.link')}}</label>
                      <input class="form-control" placeholder="{{trans('food_trucks.link')}}" type="url" name="link" value="{{$food_truck->link}}" id="link">
                    </div>
                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('food_trucks.cuisine')}}
                      </label>
                      <select class="form-control multiselect1" id="cuisines" name="cuisines[]" multiple data-placeholder="{{trans('food_trucks.select_cuisines')}}">
                        @foreach($cuisines as $cuisine)
                        <option value="{{$cuisine->id}}"
                          {{ (collect($selected_cuisines)->contains($cuisine->id)) ? 'selected':'' }}
                          >{{$cuisine->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      <select class="form-control" name="status" id="status" required>
                        <option value="active" 
                          @if($cuisine == 'active') selected @endif>
                          {{trans('common.active')}}
                        </option>
                        <option value="inactive" 
                          @if($cuisine == 'inactive') selected @endif>
                          {{trans('common.inactive')}}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('food_trucks.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-6">
                          
                              <label for="from_day" class="content-label">{{trans('food_trucks.day_from')}}</label>
                              <select class="form-control" name="from_day" id="from_day" required>
                                @foreach([0,1,2,3,4,5,6] as $day)
                                <option value="{{$day}}" @if($day == $food_truck_working_hours->from_day) selected @endif>{{trans('food_trucks.working_hours.'.$day)}}
                                </option>
                                @endforeach
                              </select>
                           
                              <label for="status" class="content-label">{{trans('food_trucks.time_from')}}</label>
                              <input name="from_time" required class="form-control" type="time" value="{{$food_truck_working_hours->from_time}}">   
                        </div>
                        <div class="col-md-6">
                          <label for="status" class="content-label">{{trans('food_trucks.day_to')}}</label>
                          <select class="form-control" name="to_day" id="status" required>
                            @foreach([0,1,2,3,4,5,6] as $day2)
                            <option value="{{$day2}}" @if($day2 == $food_truck_working_hours->to_day) selected @endif>{{trans('food_trucks.working_hours.'.$day2)}}
                            </option>
                            @endforeach
                          </select>
                       
                          <label for="status" class="content-label">{{trans('food_trucks.time_to')}}</label>
                          <input name="to_time" required class="form-control" type="time" value="{{$food_truck_working_hours->to_time}}">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('food_trucks.banners')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="banners[]" id="banners">
                      @foreach($food_truck_banners as $rbanner)
                        <div class="food_truck_media">
                          <img id="{{$rbanner['id']}}" src="{{asset($rbanner['media_path'])}}">
                          <i class="fa fa-close food_truck_media_item" data_id="{{$rbanner['id']}}"></i>
                          <p class="loading">
                            <i class="fa fa-circle-o-notch fa-spin"></i>
                          </p>
                        </div>
                      @endforeach
                    </div>
                    <div class="form-group">
                      <label for="menus" class="content-label">{{trans('food_trucks.menus')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="menus[]" id="menus">
                      @foreach($food_truck_menus as $rmenu)
                        <div class="food_truck_media">
                          <img id="{{$rmenu['id']}}" src="{{asset($rmenu['media_path'])}}">
                          <i class="fa fa-close food_truck_media_item" data_id="{{$rmenu['id']}}"></i>
                        </div>
                      @endforeach
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

    $('.food_truck_media_item').click(function(){
      var media_id = $(this).attr('data_id');
      var $this = $(this);
      $.ajax({
            type:'post',
            url: "{{route('remove_food_truck_media')}}",
            data: {
                    "media_id" : media_id,  
                    "_token": "{{ csrf_token() }}"
                  },
            beforeSend: function () {
                $this.next('.loading').css('visibility', 'visible');
            },
            // async:false,
            success: function (data) {
              if(data.success){
                $this.parent().remove();
                toastr.success(data.success);
              }else{
                toastr.error(data.error);
              }
            },
            error: function (data) {
              toastr.error("{{ trans('common.something_went_wrong') }}");
            }
        })
    })

    $('#cuisines_input').attr('autocomplete','off');
</script>
@endsection