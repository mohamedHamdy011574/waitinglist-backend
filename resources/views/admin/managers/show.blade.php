@extends('layouts.admin')

@section('css')
  <style type="text/css">
    .details{padding: 10px; background: #efebeb}
  </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('managers.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('managers.index')}}">{{trans('managers.singular')}}</a></li>
      <li class="active">{{ trans('managers.edit') }} </li>
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
                <h3 class="box-title">{{ trans('managers.details') }}</h3>
                <ul class="pull-right">
                    <a href="{{route('managers.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
            </div>
          <div class="box-body">
            <form method="POST" id="managers_form" action="{{route('managers.update',$manager->id)}}" accept-charset="UTF-8">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="model-body">
                <div class="row">
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('managers.first_name')}}</label>
                      <p class="details">{{$manager->first_name}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.last_name')}}</label>
                      <p class="details">{{$manager->last_name}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.email')}}</label>
                      <p class="details">{{$manager->email}}</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('managers.phone_number')}}</label>
                      <p class="details">{{$manager->phone_number}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.password')}}</label>
                      <p class="details">********</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.password_confirmation')}}</label>
                      <p class="details">********</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('managers.restaurant_branch')}}</label>
                      <p class="details">
                        @foreach($restaurant_branches as $restaurant_branch)
                          {{ ($m_restaurant_branch->id == $restaurant_branch->id) ? $restaurant_branch->name :'' }}
                        @endforeach
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('managers.edit', $manager->id)}}">{{trans('common.edit')}}</a>
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
    //Initialize Select2 Elements
    $('.select2').select2();

    CKEDITOR.replaceAll();
</script>

@endsection