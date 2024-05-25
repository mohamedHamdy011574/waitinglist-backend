@extends('layouts.admin')

@section('css')
<style>
  .details{padding: 10px; background: #efebeb; pointer-events: none;}
  .main_services {text-decoration: underline;}
  .main_services {text-decoration: underline; cursor: pointer; color: #337ab7; margin-left: 5px}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('reservation_hours.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('reservation_hours.index')}}">{{trans('reservation_hours.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('reservation_hours.show') }}
      </li>
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
            <h3 class="box-title">{{ trans('reservation_hours.details') }}</h3>
            @can('reservation-hours-list')
            <ul class="pull-right">
                <a href="{{route('reservation_hours.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" action="{{route('reservation_hours.update', $reservation_hour->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              @csrf
              <input name="_method" type="hidden" value="PUT">
              <div class="model-body">
                <div class="row">
                  <div class="col-md-12">
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
                            <label for="shift_name:{{$lk}}" class="content-label">{{trans('reservation_hours.shift_name')}}</label>
                            <p class="details">{{$reservation_hour->translate($lk)->shift_name}}
                            </p>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reservation_hours.from_time')}}</label>
                          <p class="details">
                            {{date('h:i A',strtotime($reservation_hour->from_time))}}
                          </p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reservation_hours.to_time')}}</label>
                          <p class="details">
                            {{date('h:i A',strtotime($reservation_hour->to_time))}}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reservation_hours.allowed_chair')}}</label>
                          <p class="details">
                            {{$reservation_hour->allowed_chair}}
                          </p>
                        </div>
                      </div>                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <p class="details"> 
                            @if($reservation_hour->status == 'active') {{trans('common.active')}}
                            @endif
                            @if($reservation_hour->status == 'inactive') {{trans('common.inactive')}}
                            @endif
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                @can("reservation-hours-edit")
                  <a class="btn btn-danger btn-fill btn-wd" href="{{route('reservation_hours.edit',$reservation_hour->id)}}">
                    {{trans('common.edit')}}
                  </a>
                @endcan
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
  function langfileds(){
    return true;
  }
</script>
@endsection