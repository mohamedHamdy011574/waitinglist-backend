@extends('layouts.admin')

@section('css')
  <style type="text/css">
    .details{padding: 10px; background: #efebeb}
  </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('admins.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('admins.index')}}">{{trans('admins.singular')}}</a></li>
      <li class="active">{{ trans('admins.edit') }} </li>
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
                <h3 class="box-title">{{ trans('admins.details') }}</h3>
                <ul class="pull-right">
                    <a href="{{route('admins.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
            </div>
          <div class="box-body">
            <form method="POST" id="admins_form" action="{{route('admins.update',$admin->id)}}" accept-charset="UTF-8">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="model-body">
                <div class="row">
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('admins.first_name')}}</label>
                      <p class="details">{{$admin->first_name}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.last_name')}}</label>
                      <p class="details">{{$admin->last_name}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.email')}}</label>
                      <p class="details">{{$admin->email}}</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('admins.phone_number')}}</label>
                      <p class="details">{{$admin->phone_number}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.password')}}</label>
                      <p class="details">********</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.password_confirmation')}}</label>
                      <p class="details">********</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('admins.edit', $admin->id)}}">{{trans('common.edit')}}</a>
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