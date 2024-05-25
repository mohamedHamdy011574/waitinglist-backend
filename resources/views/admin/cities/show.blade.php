@extends('layouts.admin')
@section('content')
<section class="content-header">
  <h1>
    {{trans('cities.details')}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="{{route('cities.index')}}">{{trans('cities.plural')}}</a></li>
    <li class="active">{{trans('cities.details')}}</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        
        <div class="box-body">
            <div class="modal-body">
              <div class="row">
                  <div class="col-md-12">
                    <table class="table">
                      <tbody>
                        <tr>
                          <th>{{trans('cities.name')}}</th>
                          <td>{{$city->name}}</td>
                        </tr>
                        <tr>
                          <th>{{trans('cities.state')}}</th>
                          <td>{{$city->state->name}}</td>
                        </tr>
                        <tr>
                          <th>{{trans('cities.country')}}</th>
                          <td>{{$city->state->country->country_name}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
