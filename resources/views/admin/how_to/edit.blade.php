@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
       {{trans('how_to.update')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
      <li><a href="{{route('how_to.index')}}">{{trans('how_to.singular')}}</a></li>
      <li class="active">{{trans('how_to.update')}}</li>
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
            <h3 class="box-title">{{ trans('how_to.details') }}</h3>
            @can('how_to-list')
              <a href="{{route('how_to.index')}}" class="btn btn-danger pull-right">
                <i class="fa fa-arrow-left"></i>
                {{ trans('common.back') }}
              </a>
            @endcan
          </div>
          <form method="POST" id="levels" action="{{route('how_to.update', $how_to->id)}}" accept-charset="UTF-8">
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
                          <label for="question:{{$lk}}" class="content-label">{{trans('how_to.question')}}</label>
                          <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('how_to.question')}}" name="question:{{$lk}}" type="text" value="{{@$how_to->translate($lk)->question}}">
                          @if ($errors->has('question:'.$lk)) <p style="color:red;">   {{ $errors->first('question:'.$lk) }}</p> 
                          @endif
                          <strong class="help-block"></strong>
                        </div>

                        <div class="form-group">
                          <label for="answer:{{$lk}}" class="answer-label">{{trans('how_to.answer')}}</label>
                          <textarea class="form-control" id="summary-ckeditor" minlength="2" maxlength="255" placeholder="{{trans('how_to.answer')}}" name="answer:{{$lk}}" value="">{{@$how_to->translate($lk)->answer}}</textarea>
                        </div>

                      
                      </div>
                    @endforeach                   
                    <div class="form-group">
                      <label for="display_order:{{$lk}}" class="content-label">{{trans('how_to.display_order')}}</label>
                      <input class="form-control" maxlength="255" placeholder="{{trans('how_to.display_order')}}" name="display_order" type="number" value="{{$how_to->display_order}}">
                       @if ($errors->has('display_order')) <p style="color:red;">   {{ $errors->first('display_order') }}</p> 
                       @endif
                      <strong class="help-block"></strong>
                    </div>
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