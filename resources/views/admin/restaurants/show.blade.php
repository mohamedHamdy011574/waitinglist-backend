@extends('layouts.admin')

@section('css')
   <style>
    .restaurant_media {display: inline-flex; }
    .restaurant_media img{width: 100px; height: 100px; padding: 5px; margin:10px 5px; background: #e5e5e5; border-radius: 6px; } 
    .restaurant_media i{font-size: 20px; color: #c10707; padding: 3px 0px; margin-right: 7px; cursor: pointer}
    .details{padding: 10px; background: #efebeb}
    .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
   </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('restaurants.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('restaurants.index')}}">{{trans('restaurants.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('restaurants.show') }}
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
            <h3 class="box-title">{{ trans('restaurants.details') }}</h3>
            @can('restaurant-list')
            <ul class="pull-right">
                <a href="{{ redirect()->getUrlGenerator()->previous() }}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('restaurants.update', $restaurant->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('restaurants.name')}}</label>
                            <p class="details">{{$restaurant->translate($lk)->name}}</p>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('restaurants.description')}}</label>
                            <div class="details">
                              {!! $restaurant->translate($lk)->description !!}</div>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('restaurants.link')}}</label>
                      <p class="details"><a href="{{$restaurant->link}}" target="_blank">{{$restaurant->link}}</a></p>
                    </div>
                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('restaurants.cuisine')}}
                      </label>
                      <p class="details">
                        @foreach($cuisines as $cuisine)
                          {!! (collect($selected_cuisines)->contains($cuisine->id)) ? $cuisine->name.'<br>' :'' !!}
                        @endforeach
                      </p>
                    </div>
                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      <p class="details">{{ucfirst($restaurant->status)}}</p>
                    </div>
                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('restaurants.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="from_day" class="content-label">{{trans('restaurants.day_from')}}</label>
                          <p class="details">{{trans('restaurants.working_hours.'.$restaurant_working_hours->from_day)}}</p>
                       
                          <label for="status" class="content-label">{{trans('restaurants.time_from')}}</label>
                          <p class="details">{{date('H:i A',strtotime($restaurant_working_hours->from_time))}}
                          </p>   
                        </div>
                        <div class="col-md-6">
                          <label for="status" class="content-label">{{trans('restaurants.day_to')}}</label>
                          <p class="details">{{trans('restaurants.working_hours.'.$restaurant_working_hours->to_day)}}</p>
                          
                       
                          <label for="status" class="content-label">{{trans('restaurants.time_to')}}</label>
                          <p class="details">{{date('H:i A',strtotime($restaurant_working_hours->to_time))}}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('restaurants.banners')}}</label>
                      <br>
                      @foreach($restaurant_banners as $rbanner)
                        <div class="restaurant_media">
                          <img id="{{$rbanner['id']}}" src="{{asset($rbanner['media_path'])}}">
                        </div>
                      @endforeach
                    </div>
                    <div class="form-group">
                      <label for="menus" class="content-label">{{trans('restaurants.menus')}}</label>
                      <br>
                      @foreach($restaurant_menus as $rmenu)
                        <div class="restaurant_media">
                          <img id="{{$rmenu['id']}}" src="{{asset($rmenu['media_path'])}}">
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  @can("restaurant-branch-edit")
                    <a class="btn btn-danger btn-fill btn-wd" href="{{route('restaurants.edit',$restaurant->id)}}">
                    {{trans('common.edit')}}
                    </a>
                  @endcan
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="form-group">
                      <label for="description" class="content-label">{{ trans('restaurant_branches.title') }}</label>
                      @if(count($restaurant->branches))
                      <table id="lots_tbl" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>{{trans('common.id')}}</th>
                            <th>{{trans('restaurant_branches.name')}}</th>
                            <th>{{trans('restaurant_branches.address')}}</th>
                            <th>{{trans('restaurant_branches.total_seats')}}</th>
                            <th>{{trans('common.action')}}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($restaurant->branches as $rb)
                            <tr>
                              <td>{{$rb->id}}</td>
                              <td><a class="btn" target="_blank" href="{{route('restaurant_branches.show',$rb->id)}}">{{$rb->name}}</a></td>
                              <td>{{$rb->address}}</td>
                              <td>{{$rb->total_seats}}</td>
                              <td>
                                @can("restaurant-branch-edit")
                                  <a class="btn" href="{{route('restaurant_branches.edit',$rb->id)}}">
                                    <i class="fa fa-edit"></i>
                                  </a>
                                @endcan  
                                <a class="btn" href="{{route('restaurant_branches.show',$rb->id)}}">
                                  <i class="fa fa-eye"></i>
                                </a>
                              </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>{{trans('common.id')}}</th>
                            <th>{{trans('restaurant_branches.name')}}</th>
                            <th>{{trans('restaurant_branches.address')}}</th>
                            <th>{{trans('restaurant_branches.total_seats')}}</th>
                            <th>{{trans('common.action')}}</th>
                          </tr>
                        </tfoot>
                      </table>
                      @else
                      <p>{{trans('common.no_data')}}</p>
                      @endif
                    </div>
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