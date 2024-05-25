@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
  td form{
    display: inline; 
  }
  .details{padding: 10px; background: #efebeb}
</style>
@endsection    
@section('content')
<section class="content-header">
  <h1>
   {{trans('food_blogs.detail')}} 
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="">{{$food_detail->recipe_name}}</a></li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <ul class="pull-right">
            <a href="{{URL::previous()}}" class="btn btn-danger">
                <i class="fa fa-arrow-left"></i>
                {{ trans('common.back') }}
            </a>
          </ul>
          <h3 class="box-title"> {{$food_detail->recipe_name}}</h3>
          <br><h6>{{trans('food_blogs.by')}} {{$blogger_name}} </h6>
        </div>
        <div class="box-body">
          @if(!empty($food_detail->recipe_video))
            @if($food_detail->is_link == 0)
              <video width="320" height="240" controls>
                <source src="{{asset($food_detail->recipe_video)}}">
              </video>
            @else       
              <a href="{{$food_detail->recipe_video}}">{{$food_detail->recipe_video}}</a>
            @endif
          @else
             {{trans('food_blogs.no_video')}}
          @endif 
          <h3><p class="details">{{$food_detail->recipe_name}} </p></h3>
          <h5>
            <p class="details">{{trans('food_blogs.category')}}{{$food_detail->cuisine->name}}
            </p>
          </h5>
          <p class="details"> {{$food_detail->description}}</p> 
        </div>
      </div>
    </div>
  </div>
</section>

@endsection