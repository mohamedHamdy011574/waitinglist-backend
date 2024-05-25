@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{ trans('coupons.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('coupons.index')}}">{{trans('coupons.singular')}}</a></li>
      <li class="active">{{ trans('coupons.edit') }} </li>
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
            <form method="POST" id="coupons_form" action="{{route('coupons.update',$coupon->id)}}" accept-charset="UTF-8">
              <div class="box-body">
                <input name="_method" type="hidden" value="PUT">
                @csrf
                <div class="model-body">
                  <div class="row">
                    
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="name"> {{trans('coupons.name')}}</label>
                        <input class="form-control" placeholder="{{trans('coupons.name')}}" required="true" name="name" type="text" id="name" value="{{$coupon->name}}">
                      </div>
                      <div class="form-group">
                        <label for="name"> {{trans('coupons.code')}}</label>
                        <input class="form-control" placeholder="{{trans('coupons.code')}}" required="true" name="code" type="text" id="code" value="{{$coupon->code}}">
                      </div>
                      <div class="form-group">
                        <label for="name"> {{trans('coupons.discount')}}</label>
                        <input class="form-control" placeholder="{{trans('coupons.discount')}}" required="true" name="discount" type="number" min="1" max="100" id="discount" value="{{$coupon->discount}}">
                      </div>
                    </div>
                      
                    <div class="col-md-4">
                      <div class="form-group">
                          <label for="name"> {{trans('coupons.description')}}</label>
                          <textarea class="form-control" placeholder="{{trans('coupons.description')}}" id="description" name="description" >{{$coupon->description}}</textarea>
                      </div>
                      <div class="form-group">
                        <label for="name"> {{trans('coupons.start_date')}}</label>
                        <input class="form-control datetimepicker" placeholder="{{trans('coupons.start_date')}}" required="true" name="start_date" type="text" id="start_date" value="{{$coupon->start_date}}">
                      </div>
                      <div class="form-group">
                        <label for="name"> {{trans('coupons.end_date')}}</label>
                        <input class="form-control datetimepicker" placeholder="{{trans('coupons.end_date')}}" required="true" name="end_date" type="text" id="end_date" value="{{$coupon->end_date}}">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="restaurants" class="content-label">
                          {{trans('coupons.choose_restaurants')}}
                        </label>
                        <select class="form-control multiselect1" id="restaurants" name="restaurants[]" multiple data-placeholder="{{trans('restaurants.select_cuisines')}}">
                          @foreach($restaurants as $restaurant)
                          <option value="{{$restaurant->id}}"
                            {{ (collect($selected_restaurants)->contains($restaurant->id)) ? 'selected':'' }}
                            >{{$restaurant->brand_name}}
                          </option>
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

    // CKEDITOR.replaceAll();
</script>

  <script type="text/javascript">
    //DateTimepicker
    $(".datetimepicker").datetimepicker({
        format: 'YYYY-MM-DD H:mm',
        icons:{
            time: 'glyphicon glyphicon-time',
            date: 'glyphicon glyphicon-calendar',
            previous: 'glyphicon glyphicon-chevron-left',
            next: 'glyphicon glyphicon-chevron-right',
            today: 'glyphicon glyphicon-screenshot',
            up: 'glyphicon glyphicon-chevron-up',
            down: 'glyphicon glyphicon-chevron-down',
            clear: 'glyphicon glyphicon-trash',
            close: 'glyphicon glyphicon-remove'
        },
        locale : '{{config("app.locale")}}'
    });
    /*$('#datetimepicker').datetimepicker({
      format: 'YYYY-MM-DD hh:mm'
    });*/
</script>

@endsection