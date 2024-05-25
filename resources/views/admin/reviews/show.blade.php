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
      {{ trans('reviews.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('reviews.index')}}">{{trans('reviews.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('reviews.show') }}
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
            <h3 class="box-title">{{ trans('reviews.details') }}</h3>
            @can('restaurant-list')
            <ul class="pull-right">
                <a href="{{route('reviews.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" action="{{route('reviews.update', $review->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              @csrf
              <input name="_method" type="hidden" value="PUT">
              <div class="model-body">                
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reviews.recipe_name')}}</label>
                          <p class="details"> 
                            {{$review->food_blog->recipe_name}}
                          </p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reviews.review')}}</label>
                          <p class="details"> 
                            {{$review->review}}
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('reviews.added_by')}}</label>
                          <p class="details"> 
                            {{$review->customer->first_name.' '.$review->customer->last_name}}
                          </p>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <p class="details"> 
                            @if($review->status == 'active') {{trans('common.active')}}
                            @endif
                            @if($review->status == 'inactive') {{trans('common.inactive')}}
                            @endif
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <p style="font-size: 20px; font-weight: bold">
                    {{trans('reviews.reports')}}
                  </p>
                    @foreach($review->reports as $key => $report)
                      <p style="font-size: 15px; font-weight: bold">{{$key + 1}}.</p>
                      <div style="background: #f7f5f5; padding:20px; margin-bottom: 20px">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="link" class="content-label">{{trans('reviews.concern')}}</label>
                              <p class="details">{{$report->concern->concern}}</p>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="link" class="content-label">{{trans('reviews.comment')}}</label>
                              <p class="details">{{$report->comment}}</p>
                            </div>
                          </div>                          
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="link" class="content-label">{{trans('reviews.reported_by')}}</label>
                              <p class="details">{{$report->customer->first_name.' '.$report->customer->last_name}}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
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