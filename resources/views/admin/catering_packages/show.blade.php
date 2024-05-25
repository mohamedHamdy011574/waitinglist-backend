@extends('layouts.admin')

@section('css')
<style>
  .details{padding: 10px; background: #efebeb}
    .catering_package_media {display: inline-flex; }
    .catering_package_media img{width: 100px; height: 100px; padding: 5px; margin:10px 5px; background: #e5e5e5; border-radius: 6px; } 
    .catering_package_media i{font-size: 20px; color: #c10707; padding: 3px 0px; margin-right: 7px; cursor: pointer}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering_packages.show') }}
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
        {{ trans('catering_packages.show') }}
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
            <form method="POST" id="message_templateForm" action="{{route('catering_packages.update', $catering_package->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <div class="row">
                              <div class="col-md-12">
                                <label for="package_name:{{$lk}}" class="content-label">{{trans('catering_packages.package_name')}}</label>
                                <p class="details">{{$catering_package->translate($lk)->package_name}}
                                </p>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                    <label for="food_serving:{{$lk}}" class="content-label">{{trans('catering_packages.food_serving')}}</label>
                                    <div class="details">{!!$catering_package->translate($lk)->food_serving!!}
                                    </div>
                                </div>
                              </div>
                            </div>
                          </div> 
                        </div>   
                      @endforeach
                    </div>
                    <div class="row">
                      <div class="col-md-3">  
                        <div class="form-group">
                          <label for="max_time_unit   " class="content-label">{{trans('catering_packages.max_time_unit')}}</label>
                           <p class="details">{{$catering_package->max_time_unit}}</p> 
                        </div>
                      </div>
                      <div class="col-md-3">  
                        <div class="form-group">
                           <label for="brand_" class="content-label">{{trans('catering_packages.setup_time_unit')}}</label>
                            <p class="details">{{$catering_package->setup_time_unit}}</p>
                        </div>
                      </div>
                       <div class="col-md-3">
                        <div class="form-group">
                           <label for="brand_" class="content-label">{{trans('catering_packages.max_time')}}</label>
                          <p class="details">{{$catering_package->max_time}}</p>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                           <label for="brand_" class="content-label">{{trans('catering_packages.setup_time')}}</label>
                          <p class="details">{{$catering_package->setup_time}}</p> 
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                           <label for="brand_" class="content-label">{{trans('catering_packages.price')}}</label>
                           <p class="details">{{$catering_package->price}}</p>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="currency" class="content-label">{{trans('catering_addons.currency')}}</label>
                          <input name="currency" required class="form-control" readonly type="text" value="{{$currency}}">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                           <label for="brand_" class="content-label">{{trans('catering_packages.person_serve')}}</label>
                           <p class="details">{{$catering_package->person_serve}}</p>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                           <label for="brand_" class="content-label">{{trans('catering_packages.category_name')}}</label>
                            <p class="details">{{$catering_package->category->name}}</p>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="image" class="content-label">{{trans('catering_packages.image')}}</label>
                          <br>
                          @foreach($catering_package_image as $photo)
                            <div class="catering_package_media">
                              <img id="{{$photo['id']}}" src="{{asset($photo['image'])}}">
                            </div>
                          @endforeach
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <p class="details">{{$catering_package->status}}</p>                          
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <a class="btn btn-danger btn-fill btn-wd" href="{{route('catering_packages.edit',$catering_package->id)}}">
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
