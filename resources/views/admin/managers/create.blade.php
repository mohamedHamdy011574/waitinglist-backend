@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{ trans('managers.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('managers.index')}}">{{trans('managers.singular')}}</a></li>
      <li class="active">{{ trans('managers.add_new') }} </li>
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
            <form method="POST" id="managers_form" action="{{route('managers.store')}}" accept-charset="UTF-8">
              @csrf
              <div class="model-body">
                <div class="row">
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('managers.first_name')}}</label>
                      <input class="form-control" placeholder="{{trans('managers.first_name')}}" required="true" name="first_name" type="text" id="first_name" value="{{old('first_name')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.last_name')}}</label>
                      <input class="form-control" placeholder="{{trans('managers.last_name')}}" required="true" name="last_name" type="text" id="last_name" value="{{old('last_name')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.email')}}</label>
                      <input class="form-control" placeholder="{{trans('managers.email')}}" required="true" name="email" type="text" id="email" value="{{old('email')}}">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('managers.phone_number')}}</label>
                      <input class="form-control" placeholder="{{trans('managers.phone_number')}}" required="true" name="phone_number" type="text" id="phone_number" value="{{old('phone_number')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.password')}}</label>
                      <input class="form-control" placeholder="{{trans('managers.password')}}" required="true" name="password" type="password" id="password" value="{{old('password')}}">
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('managers.password_confirmation')}}</label>
                      <input class="form-control" placeholder="{{trans('managers.password_confirmation')}}" required="true" name="password_confirmation" type="password" id="password_confirmation" value="{{old('password_confirmation')}}">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('managers.restaurant_branch')}}</label>
                      <select name="restaurant_branch_id" id="restaurant_branch_id" class="form-control select2">
                        <option value="">{{trans('managers.select_restaurant_branch')}}</option>
                        @foreach($restaurant_branches as $rest_branch)
                        <option value="{{$rest_branch->id}}" 
                          {{ ((old('restaurant_branch_id') == $rest_branch->id)) ? 'selected':'' }}
                        >{{$rest_branch->name}}</option>
                        @endforeach
                      </select>
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