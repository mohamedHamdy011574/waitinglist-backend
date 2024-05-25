@extends('layouts.admin')

@section('css')
   <style>
    .catering_media {display: inline-flex; }
    .catering_media img{width: 100px; height: 100px; padding: 5px; margin:10px 5px; background: #e5e5e5; border-radius: 6px; } 
    .catering_media i{font-size: 20px; color: #c10707; padding: 3px 0px; margin-right: 7px; cursor: pointer}
    .details{padding: 10px; background: #efebeb}
    .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
   </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('catering.index')}}">{{trans('catering.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('catering.show') }}
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
            <h3 class="box-title">{{ trans('catering.details') }}</h3>
            @can('catering-list')
            <ul class="pull-right">
                <a href="{{ redirect()->getUrlGenerator()->previous() }}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('catering.update', $catering->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="model-body">
                <div class="row">
                  <div class="col-md-6">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('catering.name')}}</label>
                            <p class="details">{{$catering->translate($lk)->name}}</p>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('catering.description')}}</label>
                            <div class="details">
                              {!! $catering->translate($lk)->description !!}</div>
                          </div>
                          <div class="form-group">
                          <label for="food_serving:{{$lk}}" class="content-label">{{trans('catering.food_serving')}}</label>
                          <div class="details">
                            {!! $catering->translate($lk)->food_serving !!}
                          </div>
                        </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('catering.link')}}</label>
                      <p class="details"><a href="{{$catering->link}}" target="_blank">{{$catering->link}}</a></p>
                    </div>
                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('catering.cuisine')}}
                      </label>
                      <p class="details">
                        @foreach($cuisines as $cuisine)
                          {!! (collect($selected_cuisines)->contains($cuisine->id)) ? $cuisine->name.'<br>' :'' !!}
                        @endforeach
                      </p>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <p class="details">{{ucfirst($catering->status)}}</p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="price" class="content-label">{{trans('catering.price')}} {{$currency}}</label>
                          <div class="details">
                            {!! $catering->price !!}
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="country" class="content-label">
                              {{trans('catering.country')}}
                            </label>
                            
                            <p class="details">
                              @foreach($countries as $ctry)
                                {{ ($selected_country->country->id == $ctry->id) ? $ctry->country_name:'' }}
                              @endforeach
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="cuisines" class="content-label">
                              {{trans('catering.state')}}
                            </label>
                            
                            <p class="details">
                              @foreach($states as $state)
                                {{ ($catering->state_id == $state->id) ? $state->name:'' }}
                              @endforeach
                            </p>  
                          </div>
                        </div>
                      </div>

                      
                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('catering.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="from_day" class="content-label">{{trans('catering.day_from')}}</label>
                          <p class="details">{{trans('catering.working_hours.'.$catering_working_hours->from_day)}}</p>
                       
                          <label for="status" class="content-label">{{trans('catering.time_from')}}</label>
                          <p class="details">{{date('H:i A',strtotime($catering_working_hours->from_time))}}
                          </p>   
                        </div>
                        <div class="col-md-6">
                          <label for="status" class="content-label">{{trans('catering.day_to')}}</label>
                          <p class="details">{{trans('catering.working_hours.'.$catering_working_hours->to_day)}}</p>
                          
                       
                          <label for="status" class="content-label">{{trans('catering.time_to')}}</label>
                          <p class="details">{{date('H:i A',strtotime($catering_working_hours->to_time))}}
                          </p>
                        </div>
                      </div>
                      
                    </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('catering.banners')}}</label>
                      <br>
                      @foreach($catering_banners as $rbanner)
                        <div class="catering_media">
                          <img id="{{$rbanner['id']}}" src="{{asset($rbanner['media_path'])}}">
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <a class="btn btn-danger btn-fill btn-wd" href="{{route('catering.edit',$catering->id)}}">
                  {{trans('common.edit')}}
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
<!-- <script src="{{ asset('admin/bower_components/ckeditor/ckeditor.js') }}"></script> -->
@section('js')
<script>
    // CKEDITOR.replaceAll();
</script>

<script>
    //Initialize Select2 Elements
    $('.select2').select2();
</script>
@endsection