@extends('layouts.admin')
@section('css')
   <style>   
    .details{padding: 10px; background: #efebeb}
   </style>
@endsection
@section('content')
  <section class="content-header">
    <h1>
       {{trans('cms.singular') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('cms.index')}}">{{trans('cms.show')}}</a></li>
      <li class="active">{{ trans('cms.singular') }}</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
           <div class="box-header with-border">
                <h3 class="box-title">{{ trans('cms.cms_detail') }}</h3>
                @can('cms-list')
                <ul class="pull-right">
                    <a href="{{route('cms.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
                @endcan
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
              </div>
                <div class="tab-content" style="margin-top: 10px;">
                  @foreach(config('app.locales') as $lk=>$lv)
                    <div role="tabpanel" class="tab-pane @if($lk=='en') active @endif" id="abc_{{$lk}}">
                      <div class="form-group">  
                        <label for="content:{{$lk}}" class="content-label">{{trans('cms.content')}}</label><br>
                        <div class="details"> 
                          {!!@$cms->translate($lk)->content!!}
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="page_name:{{$lk}}" class="content-label">{{trans('cms.page_name')}}</label><br>
                        <p class="details"> 
                          {{@$cms->translate($lk)->page_name}}
                        </p> 
                        <strong class="help-block"></strong>
                      </div>

                    </div>
                  @endforeach

                    <div class="form-group">
                        <label for="slug" class="content-label">{{trans('cms.slug')}}</label><br>
                        <p class="details"> {{$cms->slug}} </p>
                          @if ($errors->has('slug')) <p style="color:red;">{{ $errors->first('slug') }}</p> @endif
                      <strong class="help-block"></strong>
                    </div>
                      
                    <div class="form-group">
                      <label for="display_order:{{$lk}}" class="content-label">{{trans('cms.display_order')}}</label><br>
                      <p class="details">  {{$cms->display_order}} </p>
                       @if ($errors->has('display_order')) <p style="color:red;">{{ $errors->first('display_order') }}</p> @endif
                      <strong class="help-block"></strong>
                    </div>
                 
                </div>
                
              </div>
                  <div class="modal-footer">
 
              </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('js')
  <script src="{{ asset('admin/custom/faqs.js') }}"></script>
@endsection


