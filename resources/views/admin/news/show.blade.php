@extends('layouts.admin')
@section('css')
  <style type="text/css">
    .details{padding: 10px; background: #efebeb}
  </style>
@endsection
@section('content')
  <section class="content-header">
    <h1>
      {{trans('news.details')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
      <li><a href="{{route('news.index')}}">{{trans('news.singular')}}</a></li>
      <li class="active">{{trans('news.details')}}</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
           <div class="box-header with-border">
              <h3 class="box-title">{{ trans('news.details') }}</h3>
      
                <ul class="pull-right">
                    <a href="{{route('news.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
          </div>
          <div class="box-body">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="modal-body">

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
                        <label for="headline:{{$lk}}" class="content-label">{{trans('news.headline')}}</label>
                        <p class="details">
                          {{@$news->translate($lk)->headline}}
                        </p>
                        <strong class="help-block"></strong>
                      </div>
                      <div class="form-group">
                        <label for="description:{{$lk}}" class="content-label">{{trans('news.description')}}</label>
                        <div class="details"> {!!@$news->translate($lk)->description!!}
                        </div> 
                        <strong class="help-block"></strong>
                      </div>
                    </div>
                  @endforeach
                     <div class="form-group">
                    <label for="banner" class="content-label">{{trans('news.banner')}}</label>              
               <p>
                     <img src="{{asset($news->banner)}}" width='100' height="100">
                  </p>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

