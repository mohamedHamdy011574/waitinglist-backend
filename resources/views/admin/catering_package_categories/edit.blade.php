@extends('layouts.admin')

@section('css')
<style>
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering_package_categories.edit') }}
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
        {{ trans('catering_package_categories.edit') }}
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
            @can('catering-package-category-list')
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
                            <input class="form-control"  placeholder="{{trans('catering_package_categories.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{$catering_package_categories->translate($lk)->name}}"  
                            @if($lk=='en' || $lk=='ar') required @endif>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    
                      @endforeach
                    </div>
                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      <select class="form-control" name="status" id="status" required>
                        <option value="active" 
                          @if($catering_package_categories->status == 'active') selected @endif>
                          {{trans('common.active')}}
                        </option>
                        <option value="inactive" 
                          @if($catering_package_categories->status == 'inactive') selected @endif>
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
