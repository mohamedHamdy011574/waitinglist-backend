@extends('layouts.admin')
@section('css')
   <style>   
    .details{padding: 10px; background: #efebeb}
   </style>
@endsection
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
                          <p class="details">{{@$how_to->translate($lk)->question}}</p>
                        </div>

                        <div class="form-group">
                          <label for="answer:{{$lk}}" class="answer-label">{{trans('how_to.answer')}}</label>
                          <div class="details">
                            {!! @$how_to->translate($lk)->answer !!}
                          </div>
                        </div>

                      
                      </div>
                    @endforeach                   
                    <div class="form-group">
                      <label for="display_order:{{$lk}}" class="content-label">{{trans('how_to.display_order')}}</label>
                      <p class="details">{{$how_to->display_order}}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <a class="btn btn-danger btn-fill btn-wd" href="{{route('how_to.edit',$how_to->id)}}">
                  {{trans('common.edit')}}
                </a>
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