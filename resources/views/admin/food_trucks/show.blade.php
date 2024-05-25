@extends('layouts.admin')

@section('css')
   <style>
    .food_truck_media {display: inline-flex; }
    .food_truck_media img{width: 100px; height: 100px; padding: 5px; margin:10px 5px; background: #e5e5e5; border-radius: 6px; } 
    .food_truck_media i{font-size: 20px; color: #c10707; padding: 3px 0px; margin-right: 7px; cursor: pointer}
    .details{padding: 10px; background: #efebeb}
    .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
   </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('food_trucks.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('food_trucks.index')}}">{{trans('food_trucks.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('food_trucks.show') }}
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
            <h3 class="box-title">{{ trans('food_trucks.details') }}</h3>
            @can('food-truck-list')
            <ul class="pull-right">
                <a href="{{ redirect()->getUrlGenerator()->previous() }}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('food_trucks.update', $food_truck->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('food_trucks.name')}}</label>
                            <p class="details">{{$food_truck->translate($lk)->name}}</p>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('food_trucks.description')}}</label>
                            <div class="details">
                              {!! $food_truck->translate($lk)->description !!}</div>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('food_trucks.link')}}</label>
                      <p class="details"><a href="{{$food_truck->link}}" target="_blank">{{$food_truck->link}}</a></p>
                    </div>
                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('food_trucks.cuisine')}}
                      </label>
                      <p class="details">
                        @foreach($cuisines as $cuisine)
                          {!! (collect($selected_cuisines)->contains($cuisine->id)) ? $cuisine->name.'<br>' :'' !!}
                        @endforeach
                      </p>
                    </div>
                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      <p class="details">{{ucfirst($food_truck->status)}}</p>
                    </div>
                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('food_trucks.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="from_day" class="content-label">{{trans('food_trucks.day_from')}}</label>
                          <p class="details">{{trans('food_trucks.working_hours.'.$food_truck_working_hours->from_day)}}</p>
                       
                          <label for="status" class="content-label">{{trans('food_trucks.time_from')}}</label>
                          <p class="details">{{date('H:i A',strtotime($food_truck_working_hours->from_time))}}
                          </p>   
                        </div>
                        <div class="col-md-6">
                          <label for="status" class="content-label">{{trans('food_trucks.day_to')}}</label>
                          <p class="details">{{trans('food_trucks.working_hours.'.$food_truck_working_hours->to_day)}}</p>
                          
                       
                          <label for="status" class="content-label">{{trans('food_trucks.time_to')}}</label>
                          <p class="details">{{date('H:i A',strtotime($food_truck_working_hours->to_time))}}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('food_trucks.banners')}}</label>
                      <br>
                      @foreach($food_truck_banners as $rbanner)
                        <div class="food_truck_media">
                          <img id="{{$rbanner['id']}}" src="{{asset($rbanner['media_path'])}}">
                        </div>
                      @endforeach
                    </div>
                    <div class="form-group">
                      <label for="menus" class="content-label">{{trans('food_trucks.menus')}}</label>
                      <br>
                      @foreach($food_truck_menus as $rmenu)
                        <div class="food_truck_media">
                          <img id="{{$rmenu['id']}}" src="{{asset($rmenu['media_path'])}}">
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <a class="btn btn-danger btn-fill btn-wd" href="{{route('food_trucks.edit',$food_truck->id)}}">
                  {{trans('common.edit')}}
                  </a>
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="form-group">
                      <label for="description" class="content-label">{{ trans('food_truck_branches.title') }}</label>
                      @if(count($food_truck->branches))
                      <table id="lots_tbl" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>{{trans('common.id')}}</th>
                            <th>{{trans('food_truck_branches.name')}}</th>
                            <th>{{trans('food_truck_branches.address')}}</th>
                            <th>{{trans('food_truck_branches.total_seats')}}</th>
                            <th>{{trans('common.action')}}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($food_truck->branches as $rb)
                            <tr>
                              <td>{{$rb->id}}</td>
                              <td><a class="btn" target="_blank" href="{{route('food_truck_branches.show',$rb->id)}}">{{$rb->name}}</a></td>
                              <td>{{$rb->address}}</td>
                              <td>{{$rb->total_seats}}</td>
                              <td>
                                <a class="btn" href="{{route('food_truck_branches.edit',$rb->id)}}">
                                  <i class="fa fa-edit"></i>
                                </a>
                                <a class="btn" href="{{route('food_truck_branches.show',$rb->id)}}">
                                  <i class="fa fa-eye"></i>
                                </a>
                              </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>{{trans('common.id')}}</th>
                            <th>{{trans('food_truck_branches.name')}}</th>
                            <th>{{trans('food_truck_branches.address')}}</th>
                            <th>{{trans('food_truck_branches.total_seats')}}</th>
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