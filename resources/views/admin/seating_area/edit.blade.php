@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{trans('seating_area.edit')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
      <li><a href="{{route('seating_area.index')}}">{{trans('seating_area.plural')}}</a></li>
      <li class="active">{{trans('seating_area.edit')}}</li>
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
            <h3 class="box-title">{{ trans('seating_area.details') }}</h3>
            @can('seating-area-list')
              <a href="{{route('seating_area.index')}}" class="btn btn-danger pull-right">
                <i class="fa fa-arrow-left"></i>
                {{ trans('common.back') }}
              </a>
            @endcan
          </div>
          <form method="POST" id="levels" action="{{route('seating_area.update', $seating_area->id)}}" accept-charset="UTF-8">
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
                          <label for="name:{{$lk}}" class="content-label">{{trans('seating_area.name')}}</label>
                          <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('seating_area.name')}}" name="name:{{$lk}}" type="text" value="{{@$seating_area->translate($lk)->name}}">
                        </div>
                      
                        <div class="form-group">
                          <label for="content:{{$lk}}" class="content-label">{{trans('seating_area.description')}}</label>
                          <textarea class="form-control" placeholder="{{trans('seating_area.description')}}" name="description:{{$lk}}">{{@$seating_area->translate($lk)->description}}</textarea>
                          @if ($errors->has('content:'.$lk)) 
                           <p style="color:red;">{{ $errors->first('content:'.$lk) }}</p>
                          @endif
                          <strong class="help-block"></strong>
                        </div>
                      </div>
                    @endforeach
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
  $('#paid_free').change(function(){
    var type = $(this).val();
    if(type == 'free'){
      $('#price_section').hide();
    }else{
      $('#price_section').show();
    }
  });
</script>
@endsection