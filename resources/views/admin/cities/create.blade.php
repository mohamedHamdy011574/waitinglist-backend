@extends('layouts.admin')
@section('content')
<section class="content-header">
  <h1>
    {{trans('cities.add_new')}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
    <li><a href="{{route('cities.index')}}">{{trans('cities.plural')}}</a></li>
    <li class="active"> {{trans('cities.add_new')}}</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('cities.details') }}</h3>
          @can('state-list')
          <ul class="pull-right">
              <a href="{{route('cities.index')}}" class="btn btn-danger">
                  <i class="fa fa-arrow-left"></i>
                  {{ trans('common.back') }}
              </a>
          </ul>
          @endcan
        </div>
        <div class="box-body">
          <form method="POST" id="cityForm" action="{{route('cities.store')}}" accept-charset="UTF-8">
            @csrf
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name"> {{trans('cities.select_state')}}</label>
                    <select name="state_id" id="states" class="form-control">
                      @foreach($states as $state)
                      <option value="{{$state->id}}">{{$state->name}}</option>
                      @endforeach
                    </select>
                    @error('state_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name"> {{trans('cities.name')}}</label>
                    <input class="form-control" placeholder="Enter City" required="true" name="name" type="text" id="name">
                  </div>
                  @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
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

@section('js')
<script type="text/javascript">var ajaxUrl = "{{ route('get_states_from_country') }}"; </script>
  <script src="{{ asset('admin/custom/cities.js') }}"></script>
@endsection