@extends('layouts.admin')
@section('content')
<section class="content-header">
  <h1>
     {{trans('states.update')}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home')}}</a></li>
    <li><a href="{{route('states.index')}}">{{ trans('states.plural')}}</a></li>
    <li class="active">{{trans('states.update')}}</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-body">
          <form id="stateForm" method="POST" action="{{route('states.update', $state->id)}}" accept-charset="UTF-8">
            <input name="_method" type="hidden" value="PUT">
            @csrf
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name"> {{trans('states.select_country')}}</label>
                    <select name="country_id" class="form-control">
                      @foreach($countries as $country)
                        <option value="{{$country->id}}" @if($country->id == $state->country_id) selected @endif>{{$country->country_name}}</option>
                      @endforeach
                    </select>
                  @error('country_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" placeholder="Enter State" required="true" name="name" type="text" value="{{$state->name}}" id="name">
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