@extends('layouts.admin')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .catering_media {display: inline-flex; margin-top: 10px}
    .catering_media img{width: 100px; height: 100px; padding: 4px; margin:6px 1px 2px 0px; background: #e5e5e5; border-radius: 6px; } 
    .catering_media i{font-size: 15px; color: #c10707; cursor: pointer}
    .loading{visibility: hidden; font-size:10px;}
    .loading i{color: green;}
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
  </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('catering.index')}}">{{trans('catering.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('catering.edit') }}
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
            <h3 class="box-title">{{ trans('catering.details') }}</h3>
            @can('catering-list')
            <ul class="pull-right">
                <a href="{{route('catering.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('catering.update', $catering->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('catering.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('catering.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{$catering->translate($lk)->name}}">
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('catering.description')}}</label>
                            <textarea class="form-control" rows="5" placeholder="{{trans('catering.description')}}" name="description:{{$lk}}" id="description:{{$lk}}">{{$catering->translate($lk)->description}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="food_serving:{{$lk}}" class="content-label">{{trans('catering.food_serving')}}</label>
                            <textarea class="form-control" rows="5" placeholder="{{trans('catering.food_serving')}}" name="food_serving:{{$lk}}" id="food_serving:{{$lk}}">{{$catering->translate($lk)->food_serving}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="address:{{$lk}}" class="content-label">{{trans('catering.address')}}</label>
                            <textarea class="form-control" rows="5" placeholder="{{trans('catering.address')}}" name="address:{{$lk}}" id="address:{{$lk}}" required>{{$catering->translate($lk)->address}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('catering.link')}}</label>
                      <input class="form-control" placeholder="{{trans('catering.link')}}" type="url" name="link" value="{{$catering->link}}" id="link">
                    </div>
                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('catering.cuisine')}}
                      </label>
                      <select class="form-control multiselect1" id="cuisines" name="cuisines[]" multiple data-placeholder="{{trans('catering.select_cuisines')}}">
                        @foreach($cuisines as $cuisine)
                        <option value="{{$cuisine->id}}"
                          {{ (collect($selected_cuisines)->contains($cuisine->id)) ? 'selected':'' }}
                          >{{$cuisine->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <select class="form-control" name="status" id="status" required>
                            <option value="active" 
                              @if($catering->status == 'active') selected @endif>
                              {{trans('common.active')}}
                            </option>
                            <option value="inactive" 
                              @if($catering->status == 'inactive') selected @endif>
                              {{trans('common.inactive')}}
                            </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="price" class="content-label">{{trans('catering.price')}} {{$currency}}</label>
                          <input name="price" required class="form-control" type="number" value="{{$catering->price}}">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="country" class="content-label">
                            {{trans('catering.country')}}
                          </label>
                            <?php //echo "<pre>asdsa";print_r($selected_country);exit; ?>
                          <select class="form-control select2" id="country" name="country_id" data-placeholder="{{trans('catering.select_country')}}" required>
                            <option value="">{{trans('catering.select_country')}}
                            </option>
                            @foreach($countries as $ctry)
                            <option value="{{$ctry->id}}"
                              {{ (@$selected_country->country->id == $ctry->id) ? 'selected':'' }}
                              >{{$ctry->country_name}}
                            </option>
                            @endforeach
                          </select>
                        </div> 
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="states" class="content-label">
                            {{trans('catering.state')}}
                          </label>
                          <select class="form-control select2" id="state" name="state_id" data-placeholder="{{trans('catering.select_state')}}" required>
                            @foreach($states as $state)
                            <option value="{{$state->id}}"
                              {{ ($catering->state_id == $state->id) ? 'selected':'' }}
                              >{{$state->name}}
                            </option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('catering.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-6">
                              <label for="from_day" class="content-label">{{trans('catering.day_from')}}</label>
                              <select class="form-control" name="from_day" id="from_day" required>
                                @foreach([0,1,2,3,4,5,6] as $day)
                                <option value="{{$day}}" @if($day == $catering_working_hours->from_day) selected @endif>{{trans('catering.working_hours.'.$day)}}
                                </option>
                                @endforeach
                              </select>
                           
                              <label for="time_from" class="content-label">{{trans('catering.time_from')}}</label>
                              <input name="from_time" required class="form-control" type="time" value="{{$catering_working_hours->from_time}}">   
                        </div>
                        <div class="col-md-6">
                          <label for="day_to" class="content-label">{{trans('catering.day_to')}}</label>
                          <select class="form-control" name="to_day" id="to_day" required>
                            @foreach([0,1,2,3,4,5,6] as $day2)
                            <option value="{{$day2}}" @if($day2 == $catering_working_hours->to_day) selected @endif>{{trans('catering.working_hours.'.$day2)}}
                            </option>
                            @endforeach
                          </select>
                       
                          <label for="time_to" class="content-label">{{trans('catering.time_to')}}</label>
                          <input name="to_time" required class="form-control" type="time" value="{{$catering_working_hours->to_time}}">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('catering.banners')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="banners[]" id="banners">
                      @foreach($catering_banners as $rbanner)
                        <div class="catering_media">
                          <img id="{{$rbanner['id']}}" src="{{asset($rbanner['media_path'])}}">
                          <i class="fa fa-close catering_media_item" data_id="{{$rbanner['id']}}"></i>
                          <p class="loading">
                            <i class="fa fa-circle-o-notch fa-spin"></i>
                          </p>
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
    CKEDITOR.replace('description:en');
    CKEDITOR.replace('description:ar');

    CKEDITOR.replace( 'food_serving:en', {
      toolbarGroups: [
        { name: 'paragraph', groups: [ 'list']},
      ]
    });
    CKEDITOR.replace( 'food_serving:ar', {
      toolbarGroups: [
        { name: 'paragraph', groups: [ 'list'],},
      ]
    });
</script>

<script>
    //Initialize Select2 Elements
    $('.select2').select2();

    $('.catering_media_item').click(function(){
      var media_id = $(this).attr('data_id');
      var $this = $(this);
      $.ajax({
            type:'post',
            url: "{{route('remove_catering_media')}}",
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