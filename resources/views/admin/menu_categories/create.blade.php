@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{ trans('menu_categories.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('menu_categories.index')}}">{{trans('menu_categories.singular')}}</a></li>
      <li class="active">{{ trans('menu_categories.add_new') }} </li>
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
                <ul class="pull-right">
                    <a href="{{route('menu_categories.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
                @endcan
            </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('menu_categories.store')}}" accept-charset="UTF-8">
              @csrf
              <div class="model-body">

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
                        <label for="content:{{$lk}}" class="content-label">{{trans('menu_categories.name')}}</label>
                        <input class="form-control" class="form-control" placeholder="{{trans('menu_categories.name')}}" name="name:{{$lk}}" type="text" value="{{old('name:'.$lk)}}"
                        @if($lk=='en') required @endif
                        >
                        <strong class="help-block">
                          {{ @$errors->first('name:'.$lk) }}
                        </strong>
                      </div>
                    </div>    

                  @endforeach
                </div> 
              </div>

              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('Submit')}}</button>
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
</script>

@endsection