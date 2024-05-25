@extends('layouts.admin')
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
                      <input class="form-control" placeholder="{{trans('admins.first_name')}}" required="true" name="first_name" type="text" id="first_name" value="{{$admin->first_name}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.last_name')}}</label>
                      <input class="form-control" placeholder="{{trans('admins.last_name')}}" required="true" name="last_name" type="text" id="last_name" value="{{$admin->last_name}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.email')}}</label>
                      <input class="form-control" placeholder="{{trans('admins.email')}}" required="true" name="email" type="text" id="email" value="{{$admin->email}}">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('admins.phone_number')}}</label>
                      <input class="form-control" placeholder="{{trans('admins.phone_number')}}" required="true" name="phone_number" type="text" id="phone_number" value="{{$admin->phone_number}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.password')}}</label>
                      <input class="form-control" placeholder="{{trans('admins.password')}}" name="password" type="password" id="password" value="">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('admins.password_confirmation')}}</label>
                      <input class="form-control" placeholder="{{trans('admins.password_confirmation')}}" name="password_confirmation" type="password" id="password_confirmation" value="">
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
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