@extends('layouts.admin')

@section('css')
  <style type="text/css">
    .details{padding: 10px; background: #efebeb}
  </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{trans('menu_categories.show')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
      <li><a href="{{route('menu_categories.index')}}">{{trans('menu_categories.plural')}}</a></li>
      <li class="active">{{trans('menu_categories.show')}}</li>
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
            <h3 class="box-title">{{ trans('menu_categories.details') }}</h3>
            @can('menu-category-list')
              <a href="{{route('menu_categories.index')}}" class="btn btn-danger pull-right">
                <i class="fa fa-arrow-left"></i>
                {{ trans('common.back') }}
              </a>
            @endcan
          </div>
            <div class="box-body">
              <div class="row">
                <div class="col-lg-12">
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
                          <label for="name:{{$lk}}" class="content-label">{{trans('menu_categories.name')}}</label>
                          <p class="details">{{$menu_category->translate($lk)->name}}</p>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              @can("menu-category-edit")
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('menu_categories.edit',$menu_category->id)}}">
                  {{trans('common.edit')}}
                </a>
              @endcan
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('js')
@endsection