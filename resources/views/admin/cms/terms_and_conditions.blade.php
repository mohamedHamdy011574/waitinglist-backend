@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
       {{trans('cms.update_terms_and_conditions')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
      <li class="active">{{trans('cms.terms_and_conditions')}}</li>
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
            <h3 class="box-title">{{ trans('cms.terms_and_conditions') }}</h3>
            @can('cms-list')
              <a href="{{route('cms.index')}}" class="btn btn-danger pull-right">
                <i class="fa fa-arrow-left"></i>
                {{ trans('common.back') }}
              </a>
            @endcan
          </div>
          <form method="POST" id="levels" action="{{route('terms_and_conditions.update', $cms->id)}}" accept-charset="UTF-8">
            @csrf
            <input name="_method" type="hidden" value="PUT">
            <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
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
                          <label for="content:{{$lk}}" class="content-label">{{trans('cms.content')}}</label>
                          <textarea class="form-control" id="summary-ckeditor" minlength="2" maxlength="255" placeholder="{{trans('cms.content')}}" name="content:{{$lk}}" value="">{{@$cms->translate($lk)->content}}</textarea>
                        </div>

                        <!-- <div class="form-group">
                          <label for="page_name:{{$lk}}" class="content-label">{{trans('cms.page_name')}}</label>
                          <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.page_name')}}" name="page_name:{{$lk}}" type="text" value="{{@$cms->translate($lk)->page_name}}">
                          @if ($errors->has('page_name:'.$lk)) <p style="color:red;">   {{ $errors->first('page_name:'.$lk) }}</p> 
                          @endif
                          <strong class="help-block"></strong>
                        </div> -->
                      </div>
                    @endforeach
                    <!-- <div class="form-group">
                      <label for="slug" class="content-label">{{trans('cms.slug')}}</label>
                      <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.slug')}}" name="slug" type="text" value="{{$cms->slug}}">
                      @if ($errors->has('slug')) <p style="color:red;">{{ $errors->first('slug') }}</p> @endif
                      <strong class="help-block"></strong>
                    </div>    -->                   
                    <!-- <div class="form-group">
                      <label for="display_order:{{$lk}}" class="content-label">{{trans('cms.display_order')}}</label>
                      <input class="form-control" maxlength="255" placeholder="{{trans('cms.display_order')}}" name="display_order" type="number" value="{{$cms->display_order}}">
                       @if ($errors->has('display_order')) <p style="color:red;">   {{ $errors->first('display_order') }}</p> 
                       @endif
                      <strong class="help-block"></strong>
                    </div> -->
                  </div>
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
  </section>
@endsection

@section('js')
<script>
    CKEDITOR.replaceAll();
    CKEDITOR.config.allowedContent = true;
</script>
@endsection