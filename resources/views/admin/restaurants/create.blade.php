@extends('layouts.admin')

@section('css')
<style>
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('restaurants.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('restaurants.index')}}">{{trans('restaurants.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('restaurants.add_new') }}
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
            <h3 class="box-title">{{ trans('restaurants.details') }}</h3>
            @can('restaurant-list')
            <ul class="pull-right">
                <a href="{{route('restaurants.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('restaurants.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('restaurants.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('restaurants.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{old('name:'.$lk)}}" required>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('restaurants.description')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('restaurants.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" required>{{old('description:'.$lk)}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('restaurants.link')}}</label>
                      <input class="form-control" placeholder="{{trans('restaurants.link')}}" type="url" name="link" value="{{old('link')}}" id="link" required>
                    </div>
                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('restaurants.cuisine')}}
                      </label>
                      <select class="form-control multiselect1" id="cuisines" name="cuisines[]" multiple data-placeholder="{{trans('restaurants.select_cuisines')}}" required>
                        @foreach($cuisines as $cuisine)
                        <option value="{{$cuisine->id}}"
                          {{ (collect(old('cuisines'))->contains($cuisine->id)) ? 'selected':'' }}
                          >{{$cuisine->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('restaurants.banners')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="banners[]" id="banners" required>
                    </div>
                    <div class="form-group">
                      <label for="menus" class="content-label">{{trans('restaurants.menus')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="menus[]" id="menus" required>
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
                    
                    <div class="form-group">
                      <label for="working_status" class="content-label">{{trans('restaurants.working_status')}}</label>
                      <select class="form-control" name="working_status" id="working_status" required>
                        <option value="available" 
                          @if(old('working_status') == 'available') selected @endif>
                          {{trans('restaurants.available')}}
                        </option>
                        <option value="busy" 
                          @if(old('working_status') == 'busy') selected @endif>
                          {{trans('restaurants.busy')}}
                        </option>
                        <option value="closed" 
                          @if(old('working_status') == 'closed') selected @endif>
                          {{trans('restaurants.closed')}}
                        </option>
                        <option value="order_suspected" 
                          @if(old('working_status') == 'order_suspected') selected @endif>
                          {{trans('restaurants.order_suspected')}}
                        </option>
                      </select>
                    </div>

                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('restaurants.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="from_day" class="content-label">{{trans('restaurants.day_from')}}</label>
                          <select class="form-control" name="from_day" id="from_day" required>
                            @foreach([0,1,2,3,4,5,6] as $day)
                            <option value="{{$day}}" @if($day == old('from_day')) selected @endif>{{trans('restaurants.working_hours.'.$day)}}
                            </option>
                            @endforeach
                          </select> 
                          <label for="status" class="content-label">{{trans('restaurants.time_from')}}</label>
                          <input name="from_time" required class="form-control" type="time" value="{{old('from_time')}}">   
                        </div>
                        <div class="col-md-6">
                          <label for="status" class="content-label">{{trans('restaurants.day_to')}}</label>
                          <select class="form-control" name="to_day" id="status" required>
                            @foreach([0,1,2,3,4,5,6] as $day2)
                            <option value="{{$day2}}" @if($day2 == old('to_day')) selected @endif>{{trans('restaurants.working_hours.'.$day2)}}
                            </option>
                            @endforeach
                          </select>
                          <label for="status" class="content-label">{{trans('restaurants.time_to')}}</label>
                          <input name="to_time" required class="form-control" type="time" value="{{old('to_time')}}">
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
@endsection