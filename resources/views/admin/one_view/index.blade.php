@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
  td form{
    display: inline; 
  },
  a.button {
    -webkit-appearance: button;
    -moz-appearance: button;
    appearance: button;

    text-decoration: none;
    color: initial;
}
</style>
@endsection 
@section('content')
<section class="content-header">
  <h1>
    {{ trans('one_view.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('one_view.index')}}">{{ trans('one_view.plural') }}</a></li>
  </ol>
</section>
<br>
<center>
  <div class="form-group row">
    <div class="col-md-3">
    </div>
    <div class="col-md-2">
        <a href="{{route('one_view.index')}}" class="btn btn-primary">{{ trans('one_view.reservation') }}</a>
    </div>
    <div class="col-md-2">
        <a href="" class="btn btn-primary">{{ trans('one_view.waiting') }}</a>
    </div>
    <div class="col-md-2">
        <a href="" class="btn btn-primary">{{ trans('one_view.pick_up') }}</a>
    </div>
    <div class="col-md-3">
    </div>
  </div>   
</center>
<br>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{ trans('one_view.upcoming_reservation') }}</h3>
          </div>
          <div class="box-body">

            @if($reservations->count() == '0')
              <center>{{ trans('one_view.no_reservation') }}</center>
            @endif

            @foreach($reservations as $res)

            <div class="col-md-4 col-sm-8 col-xs-12" >
              <div class="info-box">

                <!-- Format Date -->
                @php $date = \Carbon\Carbon::parse($res->check_in_date);
                $res->check_in_date = $date->format('dS M Y h:i A'); @endphp

                <span class="info-box-text"><b></b></span>
                <span class="info-box-text"><b>{{ trans('one_view.reservation_id') }}:</b> {{$res->id}}</span><span class="pull-right"><b>{{ trans('one_view.date') }}:</b> {{$res->check_in_date}}</span> 
                <span class="info-box-text"><b>{{ trans('one_view.name') }}:</b>{{$res->first_name}} </span>
                <span class="info-box-text"><b>{{ trans('one_view.phone_number') }}:</b>{{$res->phone_number}} </span>
                <span class="info-box-text"><b>{{ trans('one_view.reserved_chairs') }}:</b>{{$res->reserved_chairs}} </span>
                 <span class="pull-left"><a href ="" class="btn btn-danger">{{ trans('one_view.cancel') }}</a></b></span>      
                <span class="pull-right"><a href ="" class="btn btn-primary">{{ trans('one_view.served') }}</a></b></span> 
        
              </div>
            </div>

            @endforeach
          
          </div>
      </div>
    </div>
  </div>
</section>

@endsection 
