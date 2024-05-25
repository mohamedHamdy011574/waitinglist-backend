@extends('layouts.admin')

@section('css')
  <style type="text/css">
    .details{padding: 10px; background: #efebeb}
  </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('coupons.show') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('coupons.index')}}">{{trans('coupons.singular')}}</a></li>
      <li class="active">{{ trans('coupons.show') }} </li>
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
                <h3 class="box-title">{{ trans('coupons.details') }}</h3>
                <ul class="pull-right">
                    <a href="{{route('coupons.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
            </div>
          <div class="box-body">
            <form method="POST" id="coupons_form" action="{{route('coupons.update',$coupon->id)}}" accept-charset="UTF-8">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="model-body">
                <div class="row">
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('coupons.name')}}</label>
                      <p class="details">{{$coupon->name}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('coupons.code')}}</label>
                      <p class="details">{{$coupon->code}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('coupons.discount')}}</label>
                      <p class="details">{{$coupon->discount}}</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name"> {{trans('coupons.description')}}</label>
                      <p class="details">{{$coupon->description}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('coupons.start_date')}}</label>
                      <p class="details">{{$coupon->start_date}}</p>
                    </div>
                    <div class="form-group">
                      <label for="name"> {{trans('coupons.end_date')}}</label>
                      <p class="details">{{$coupon->end_date}}</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="restaurants" class="content-label">
                        {{trans('restaurants.title')}}
                      </label>
                      <p class="details">
                        @foreach($restaurants as $restaurant)
                          {!! (collect($selected_restaurants)->contains($restaurant->id)) ? $restaurant->brand_name.'<br>' :'' !!}
                        @endforeach
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('coupons.edit', $coupon->id)}}">{{trans('common.edit')}}</a>
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