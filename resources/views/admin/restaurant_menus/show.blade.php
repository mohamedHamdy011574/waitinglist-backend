@extends('layouts.admin')

@section('css')
<style>
  .restaurant_media {display: block; margin-top: 10px}
  .restaurant_media img{width: 100px; height: 100px; padding: 4px; margin:6px 1px 2px 0px; background: #e5e5e5; border-radius: 6px; } 
  .details{padding: 10px; background: #efebeb}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('restaurant_menus.edit') }}
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
        {{ trans('restaurant_menus.edit') }}
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
            <form method="POST" id="message_templateForm" action="{{route('restaurant_menus.update', $restaurant_menu->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('restaurant_menus.name')}}</label>
                            <p class="details">{{$restaurant_menu->translate($lk)->name}}
                            </p> 
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('restaurant_menus.description')}}</label>
                            <div class="details"> {!!$restaurant_menu->translate($lk)->description!!}
                            </div>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('restaurant_menus.category')}}</label>
                      @foreach($menu_categories as $mc)
                        @if($restaurant_menu->menu_category_id == $mc->id) 
                          <p class="details">{{$mc->name}}</p>
                        @endif
                      @endforeach
                    </div>
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('restaurant_menus.photo')}}</label>
                      
                      <div class="restaurant_media">
                        <img id="" src="{{asset($restaurant_menu->menu_item_photo)}}">
                      </div>
                    </div>
                  </div>

                      <div class="col-md-6">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('restaurant_menus.price')}} {{$currency}}</label>
                              <p class="details">{{$restaurant_menu->price}}</p>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="currency" id="currency" class="content-label">{{trans('restaurant_menus.currency')}} {{$currency}}</label>
                              <p class="details">{{$restaurant_menu->currency}}
                              </p>
                            </div>
                          </div>
                        </div>

                        

                        

                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                            @if($restaurant_menu->status == 'active')  
                              <p class="details">{{trans('common.active')}}</p>
                            @endif
                            @if($restaurant_menu->status == 'inactive') 
                              <p class="details">{{trans('common.active')}}</p>
                            @endif
                        </div>
                        

                    </div>
                </div>
              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('restaurant_menus.edit',$restaurant_menu->id)}}">
                  {{trans('common.edit')}}
                </a>
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