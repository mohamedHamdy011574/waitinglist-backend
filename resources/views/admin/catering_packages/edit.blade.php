@extends('layouts.admin')

@section('css')
<style>
 .meidcal_room_media {display: inline-flex; margin-top: 10px}
  .meidcal_room_media img{width: 100px; height: 100px; padding: 4px; margin:6px 1px 2px 0px; background: #e5e5e5; border-radius: 6px; } 
  .meidcal_room_media i{font-size: 15px; color: #c10707; cursor: pointer}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering_packages.update') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('catering_packages.index')}}">{{trans('catering_packages.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('catering_packages.update') }}
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
            <h3 class="box-title">{{ trans('catering_packages.details') }}</h3>
            @can('catering-package-category-list')
            <ul class="pull-right">
                <a href="{{route('catering_packages.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
             <form method="Post" id="catering_packages" action="{{route('catering_packages.update',  $catering_package->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <div class="row">
                              <div class="col-md-12">
                                <label for="package_name:{{$lk}}" class="content-label">{{trans('catering_packages.package_name')}}</label>
                                <input class="form-control"  placeholder="{{trans('catering_packages.package_name')}}" name="package_name:{{$lk}}" id="package_name:{{$lk}}" type="text" value="{{$catering_package->translate($lk)->package_name}}" 
                                @if($lk=='en' || $lk=='ar') required @endif>
                                <strong class="help-block"></strong>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                    <label for="food_serving:{{$lk}}" class="content-label">{{trans('catering_packages.food_serving')}}</label>
                                    <textarea class="form-control"  placeholder="{{trans('catering_packages.food_serving')}}" name="food_serving:{{$lk}}" id="ckeditor" type="text"  @if($lk=='en' || $lk=='ar') required @endif>{{$catering_package->translate($lk)->food_serving}} </textarea>
                                    <strong class="help-block"></strong>
                                </div>
                              </div>
                            </div>
                          </div> 
                        </div>   
                      @endforeach
                    </div>
                    <div class="row">
                      <div class="col-md-2">
                        <div class="form-group">
                         <label for="brand_" class="content-label">{{trans('catering_packages.price')}}</label>
                         <input class="form-control" placeholder="{{trans('catering_packages.price')}}" type="number" name="price" value="{{$catering_package->price}}" id="price" required>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                           <label for="brand_" class="content-label">{{trans('catering_packages.person_serve')}}</label>
                           <input class="form-control" placeholder="{{trans('catering_packages.person_serve')}}" type="number" name="person_serve" value="{{$catering_package->person_serve}}" id="person_serve" required>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="row">
                          <div class="col-md-6">  
                            <div class="form-group">
                              <label for="setup_time_unit  " class="content-label">{{trans('catering_packages.setup_time_unit')}}</label>
                              <select class="form-control" name="setup_time_unit" id="setup_time_unit" required>
                                <option value="mins" 
                                  @if($catering_package->setup_time_unit == 'mins') selected @endif>
                                  {{trans('catering_packages.min')}}
                                </option>
                                <option value="hours" 
                                  @if($catering_package->setup_time_unit  == 'hours') selected @endif>
                                  {{trans('catering_packages.hours')}}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">  
                            <div class="form-group">
                              <label for="max_time_unit   " class="content-label">{{trans('catering_packages.max_time_unit')}}</label>
                              <select class="form-control" name="max_time_unit" id="max_time_unit" required>
                                <option value="mins" 
                                  @if($catering_package->max_time_unit == 'mins') selected @endif>
                                  {{trans('catering_packages.min')}}
                                </option>
                                <option value="hours" 
                                  @if($catering_package->max_time_unit == 'hours') selected @endif>
                                  {{trans('catering_packages.hours')}}
                                </option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="row">
                          <div class="col-md-6"> 
                            <div class="form-group">
                               <label for="brand_" class="content-label">{{trans('catering_packages.setup_time')}}</label>
                               <input class="form-control" placeholder="{{trans('catering_packages.setup_time')}}" type="number" name="setup_time" value="{{$catering_package->setup_time}}" id="setup_time" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                               <label for="brand_" class="content-label">{{trans('catering_packages.max_time')}}</label>
                               <input class="form-control" placeholder="{{trans('catering_packages.max_time')}}" type="number" name="max_time" value="{{$catering_package->max_time}}" id="max_time" required>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="price" class="content-label">{{trans('catering_addons.currency')}}</label>
                          <input name="currency" required class="form-control" readonly type="text" value="{{$currency}}">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="category_name" class="content-label">{{trans('catering_packages.category_name')}}</label>
                          <select class="form-control" name='category_name'>
                            <option value="">{{trans('catering_packages.select_category')}}</option> 
                            @foreach($catering_package_category as $pkg_category)                       
                              <option value="{{$pkg_category->id}}" @if($pkg_category->id == $catering_package->catering_pkg_cat_id) selected @endif>{{$pkg_category->name}}</option>
                            @endforeach 
                          </select>
                          @error('category_name')
                          <div class="help-block">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                            <label for="image" class="content-label">{{trans('catering_packages.image')}}</label>
                            <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="image[]" id="image">
                            @foreach($catering_package_image as $photo)
                          <div class="meidcal_room_media">
                            <img id="{{$photo['id']}}" src="{{asset($photo['image'])}}">
                            <i class="fa fa-close catering_package_media_item" data_id="{{$photo['id']}}"></i>
                        </div>
                            @endforeach
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="content-label">{{trans('common.status')}}</label>
                            <select class="form-control" name="status" id="status" required>
                              <option value="active" 
                                @if($catering_package->status == 'active') selected @endif>
                                {{trans('common.active')}}
                              </option>
                              <option value="inactive" 
                                @if($catering_package->status == 'inactive') selected @endif>
                                {{trans('common.inactive')}}
                              </option>
                            </select>
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
@section('js')
<script>
  CKEDITOR.replaceAll();

   $('.catering_package_media_item').click(function(){
      var media_id = $(this).attr('data_id');
      var $this = $(this);
      $.ajax({
            type:'post',
            url:"{{route('remove_catering_package_media')}}",
            data: {
                    "media_id" : media_id,  
                    "catering_pkg_id" :{{$catering_package->id}},
                    "_token": "{{ csrf_token() }}"
                  },
            beforeSend: function () {
                $this.next('.loading').css('visibility', 'disable');
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
        });
    });
  </script>
@endsection