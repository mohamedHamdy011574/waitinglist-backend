@extends('layouts.admin')

@section('css')
<style>
  .restaurant_media {display: inline-flex; margin-top: 10px}
  .restaurant_media img{width: 100px; height: 100px; padding: 4px; margin:6px 1px 2px 0px; background: #e5e5e5; border-radius: 6px; } 
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('food_truck_menus.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('food_truck_menus.index')}}">{{trans('food_truck_menus.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('food_truck_menus.edit') }}
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
            <h3 class="box-title">{{ trans('food_truck_menus.details') }}</h3>
            @can('catering-plan-list')
            <ul class="pull-right">
                <a href="{{route('food_truck_menus.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('food_truck_menus.update', $food_truck_menu->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              <input name="_method" type="hidden" value="PUT">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('food_truck_menus.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('food_truck_menus.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{$food_truck_menu->translate($lk)->name}}" 
                            @if($lk=='en') required @endif>
                            <strong class="help-block">
                              {{ @$errors->first('name:'.$lk) }}
                            </strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('food_truck_menus.description')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('food_truck_menus.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" @if($lk=='en') required @endif>{{$food_truck_menu->translate($lk)->description}}</textarea>
                            <strong class="help-block">
                              {{ @$errors->first('description:'.$lk) }}
                            </strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('food_truck_menus.category')}}</label>
                      <select class="form-control" name="menu_category">
                        <option value="">{{trans('food_truck_menus.select_category')}}</option>
                        @foreach($menu_categories as $mc)
                          <option value="{{$mc->id}}" 
                            @if($food_truck_menu->menu_category_id == $mc->id) selected @endif
                            >{{$mc->name}}</option>
                        @endforeach
                      </select>
                      <strong class="help-block">
                        {{ @$errors->first('menu_category') }}
                      </strong>
                    </div>
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('food_truck_menus.photo')}}</label>
                      <input class="form-control" type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="menu_item_photo" id="menu_item_photo">
                      <div class="restaurant_media">
                        <img id="" src="{{asset($food_truck_menu->menu_item_photo)}}">
                      </div>
                      <strong class="help-block">
                        {{ @$errors->first('menu_item_photo') }}
                      </strong>
                    </div>
                  </div>

                      <div class="col-md-6">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('food_truck_menus.price')}} {{$currency}}</label>
                              <input name="price" required class="form-control" type="number" value="{{$food_truck_menu->price}}">
                              <strong class="help-block">
                                {{ @$errors->first('price') }}
                              </strong>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="currency" id="currency" class="content-label">{{trans('food_truck_menus.currency')}} {{$currency}}</label>
                              <input name="currency" required class="form-control" readonly type="text" value="{{$food_truck_menu->currency}}">
                              <strong class="help-block">
                                {{ @$errors->first('currency') }}
                              </strong>
                            </div>
                          </div>
                        </div>

                        

                        

                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <select class="form-control" name="status" id="status" required>
                            <option value="active" 
                              @if($food_truck_menu->status == 'active') selected @endif>
                              {{trans('common.active')}}
                            </option>
                            <option value="inactive" 
                              @if($food_truck_menu->status == 'inactive') selected @endif>
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
</script>

<!-- <script>
    //Initialize Select2 Elements
    $('#add_food_serving').click(function(){
      var food_serving_input = '<input name="food_serving[]" required class="form-control food_servings" type="text">';
      $('#food_serving_en').append(food_serving_input);
    });
</script> -->
@endsection