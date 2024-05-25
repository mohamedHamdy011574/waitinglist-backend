@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{ trans('cms.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('cms.index')}}">{{trans('cms.singular')}}</a></li>
      <li class="active">{{ trans('cms.add_new') }} </li>
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
                <h3 class="box-title">{{ trans('cms.details') }}</h3>
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
            <form method="POST" id="message_templateForm" action="{{route('cms.store')}}" accept-charset="UTF-8">
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
                        <label for="content:{{$lk}}" class="content-label">{{trans('cms.content')}}</label>
                        <textarea class="form-control" id="summary-ckeditor" class="form-control"  placeholder="{{trans('cms.content')}}" name="content:{{$lk}}" type="text">{{old('content:'.$lk)}} </textarea>
                         @if ($errors->has('content:'.$lk)) <p style="color:red;">{{ $errors->first('content:'.$lk) }}</p> @endif
                        <strong class="help-block"></strong>
                      </div>

                      <div class="form-group">
                        <label for="page_name:{{$lk}}" class="content-label">{{trans('cms.page_name')}}</label>
                        <input class="form-control" minlength="3" maxlength="150" placeholder="{{trans('cms.page_name')}}"name="page_name:{{$lk}}" type="text"  value="{{old('page_name:'.$lk)}}">
                        @if ($errors->has('page_name:'.$lk)) <p style="color:red;">{{ $errors->first('page_name:'.$lk) }}</p> @endif
                        <strong class="help-block"></strong>
                      </div>  
                    </div>    

                  @endforeach

                  <div class="form-group">
                    <label for="slug" class="content-label">{{trans('cms.slug')}}</label>
                    <input class="form-control" minlength="3" maxlength="150" placeholder="{{trans('cms.slug')}}" name="slug" type="text"  value="{{old('slug')}}">
                    @if ($errors->has('slug')) <p style="color:red;">{{ $errors->first('slug') }}</p> @endif
                    <strong class="help-block"></strong>
                  </div>

                  <div class="form-group">
                    <label for="display_order" class="content-label">{{trans('cms.display_order')}}</label>
                    <input class="form-control" placeholder="{{trans('cms.display_order')}}" name="display_order" type="number" value="{{old('display_order')}}">
                     @if ($errors->has('display_order')) <p style="color:red;">{{ $errors->first('display_order') }}</p> @endif
                    <strong class="help-block"></strong>
                  </div> 
                </div> 
              </div>
              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
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