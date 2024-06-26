@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{trans('news.create_news')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
      <li><a href="{{route('news.index')}}">{{trans('news.plural')}}</a></li>
      <li class="active">{{trans('news.create_news')}}</li>
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
             <h3 class="box-title">{{ trans('news.details') }}</h3>
                <ul class="pull-right">
                    <a href="{{route('news.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
          </div>
          <div class="box-body">
            <form method="POST" id="faqForm" action="{{route('news.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                        <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('news.headline')}}" name="headline:{{$lk}}" type="text" value="{{old('headline:'.$lk)}}" >
                        <strong class="help-block"></strong>
                      </div>
                      <div class="form-group">
                        <label for="description:{{$lk}}" class="content-label">{{trans('news.description')}}</label>
                        <textarea class="form-control" minlength="2" maxlength="255" id="ckeditor" placeholder="{{trans('news.description')}}" name="description:{{$lk}}">{{old('description:'.$lk)}}</textarea>
                        <strong class="help-block"></strong>
                      </div>
                    </div>
                  @endforeach
                </div>
                  <div class="form-group">
                    <label for="banner" class="content-label">{{trans('news.banner')}}</label>
                    <input type="file" name="banner" id="banner"  accept=".someext,image/*" class='form-control'>
                  </div>
                </div>
              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{ trans('common.submit') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('js')
<script>
CKEDITOR.replaceAll();
</script>
@endsection
