@extends('layouts.admin')

@section('css')
<style>
  .details{padding: 10px; background: #efebeb}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('advertisements.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('advertisements.index')}}">{{trans('advertisements.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('advertisements.edit') }}
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
            <h3 class="box-title">{{ trans('advertisements.details') }}</h3>
            @can('catering-plan-list')
            <ul class="pull-right">
                <a href="{{route('advertisements.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('advertisements.update', $advertisement->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              <input name="_method" type="hidden" value="PUT">
              @csrf
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('advertisements.name')}}</label>
                            <p class="details">{{$advertisement->translate($lk)->name}}
                            </p> 
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('advertisements.video')}}</label>
                      <strong class="help-block">
                        {{ @$errors->first('video') }}
                      </strong>
                      <video width="200" controls>
                        <source src="{{asset($advertisement->video)}}" type="video/mp4">
                      </video>
                    </div>
                  </div>

                      <div class="col-md-6">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="duration_from" class="content-label">{{trans('advertisements.duration_from')}}</label>
                              <p class="details">{{$advertisement->duration_from}}</p>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="duration_to" class="content-label">{{trans('advertisements.duration_to')}}</label>
                              <p class="details">{{$advertisement->duration_to}}</p>
                            </div>
                          </div>
                        </div>


                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                            <p class="details">{{$advertisement->status}}</p>
                        </div>
                    </div>
                </div>
              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('advertisements.edit',$advertisement->id)}}">
                  {{trans('common.edit')}}
                </a>
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


<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{ asset('admin/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<script src="{{ asset('admin/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

<script>
  
    CKEDITOR.replaceAll();

    //upload files limit
    function checkfiles() {
      var $fileUpload = $("input[type='file']");
      if (parseInt($fileUpload.get(0).files.length) > 5){
        alert("You are only allowed to upload a maximum of 5 files");
          return false;
      }
    }

    //Date picker
    $('#duration_from').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
    }).change(function(){
      var duration_to = $(this).val();

      $('#duration_to').val(moment(moment(duration_to)).add(1, 'M').format('YYYY-MM-DD'));
    })
</script>

<!-- <script>
    //Initialize Select2 Elements
    $('#add_food_serving').click(function(){
      var food_serving_input = '<input name="food_serving[]" required class="form-control food_servings" type="text">';
      $('#food_serving_en').append(food_serving_input);
    });
</script> -->
@endsection