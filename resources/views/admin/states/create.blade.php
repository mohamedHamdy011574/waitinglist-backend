@extends('layouts.admin')
@section('content')
<section class="content-header">
  <h1>
     {{ trans('states.add_new')}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home')}}</a></li>
    <li><a href="{{route('states.index')}}">{{ trans('states.plural')}}</a></li>
    <li class="active">{{ trans('states.add_new')}}</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
                <h3 class="box-title">{{ trans('states.details') }}</h3>
                @can('state-list')
                <ul class="pull-right">
                    <a href="{{route('states.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
                @endcan
            </div>
        <div class="box-body">
          <form method="POST" id="stateForm" action="{{route('states.store')}}" accept-charset="UTF-8">
            @csrf
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name"> {{ trans('states.select_country')}}</label>
                    <select name="country_id" class="form-control">
                      @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->country_name}}</option>
                      @endforeach
                    </select>
                  @error('country_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name">{{ trans('states.name') }}</label>
                    <input class="form-control" placeholder="Enter State" required="true" name="name" type="text" id="name">
                  </div>
                  @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('js')
  <script src="{{ asset('admin/custom/states.js') }}"></script>
@endsection