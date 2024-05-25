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
      {{ trans('restaurant_menus.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('restaurant_menus.index')}}">{{trans('restaurant_menus.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('restaurant_menus.add_new') }}
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
            <h3 class="box-title">{{ trans('restaurant_menus.details') }}</h3>
            @can('catering-plan-list')
            <ul class="pull-right">
                <a href="{{route('restaurant_menus.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('restaurant_menus.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('restaurant_menus.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('restaurant_menus.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{old('name:'.$lk)}}" 
                            @if($lk=='en') required @endif>
                            <strong class="help-block">
                              {{ @$errors->first('name:'.$lk) }}
                            </strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('restaurant_menus.description')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('restaurant_menus.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" @if($lk=='en') required @endif>{{old('description:'.$lk)}}</textarea>
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
                      <label for="banners" class="content-label">{{trans('restaurant_menus.category')}}</label>
                      <select class="form-control" name="menu_category">
                        <option value="">{{trans('restaurant_menus.select_category')}}</option>
                        @foreach($menu_categories as $mc)
                          <option value="{{$mc->id}}" 
                            @if(old('menu_category') == $mc->id) selected @endif
                            >{{$mc->name}}</option>
                        @endforeach
                      </select>
                      <strong class="help-block">
                        {{ @$errors->first('menu_category') }}
                      </strong>
                    </div>
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('restaurant_menus.photo')}}</label>
                      <input class="form-control" type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="menu_item_photo" id="menu_item_photo" required>
                      <strong class="help-block">
                        {{ @$errors->first('menu_item_photo') }}
                      </strong>
                    </div>
                  </div>

                      <div class="col-md-6">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('restaurant_menus.price')}} {{$currency}}</label>
                              <input name="price" required class="form-control" type="number" value="{{old('price')}}">
                              <strong class="help-block">
                                {{ @$errors->first('price') }}
                              </strong>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="currency" id="currency" class="content-label">{{trans('restaurant_menus.currency')}} {{$currency}}</label>
                              <input name="currency" required class="form-control" readonly type="text" value="{{$currency}}">
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