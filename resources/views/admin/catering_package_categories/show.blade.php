@extends('layouts.admin')

@section('css')
<style>
  .details{padding: 10px; background: #efebeb}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering_package_categories.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('catering_package_categories.index')}}">{{trans('catering_package_categories.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('catering_package_categories.show') }}
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
            <h3 class="box-title">{{ trans('catering_package_categories.details') }}</h3>
            @can('catering-package-category-list-list')
            <ul class="pull-right">
                <a href="{{route('catering_package_categories.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('catering_package_categories.update', $catering_package_categories->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('catering_package_categories.name')}}</label>
                            <p class="details">{{$catering_package_categories->translate($lk)->name}}
                            </p>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    
                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      @if($catering_package_categories->status == 'active')
                      <p class="details">{{trans('common.active')}}</p>
                      @endif
                    
                      @if($catering_package_categories->status == 'inactive')
                      <p class="details">{{trans('common.inactive')}}</p>
                      @endif
                    </div>
                  </div>
                </div>
              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('catering_package_categories.edit',$catering_package_categories->id)}}">
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
