@extends('layouts.admin')

@section('css')
  <style type="text/css">
    .details{padding: 10px; background: #efebeb}
  </style>
@endsection



@section('content')
  <section class="content-header">
    <h1>
      {{trans('seating_area.show')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
      <li><a href="{{route('seating_area.index')}}">{{trans('seating_area.plural')}}</a></li>
      <li class="active">{{trans('seating_area.show')}}</li>
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
                          <p class="details">{{@$seating_area->translate($lk)->name}}</p>
                        </div>
                        <div class="form-group">
                          <label for="description:{{$lk}}" class="content-label">{{trans('seating_area.description')}}</label>
                          <p class="details">{{@$seating_area->translate($lk)->description}}</p>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              @can("seating-area-edit")
              <a class="btn btn-danger btn-fill btn-wd" href="{{route('seating_area.edit', $seating_area->id)}}">{{trans('common.edit')}}</a>
              @endcan
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