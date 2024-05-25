@extends('layouts.admin')
@section('content')
<section class="content-header">
  <h1>
    {{trans('countries.add_new')}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="{{route('countries.index')}}">{{trans('countries.plural')}}</a></li>
    <li class="active">{{trans('countries.add_new')}}</li>
  </ol>
</section>
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('countries.details') }}</h3>
                @can('country-list')
                <ul class="pull-right">
                    <a href="{{route('countries.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
                @endcan
            </div>
            <div class="box-body">
                {!! Form::open(['route' => 'countries.store','method' => 'POST','id'=>'country']) !!}
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group @error('country_name') ? has-error : ''  @enderror">
                                    {{Form::label('name',trans('countries.country_name'))}}
                                    {!!Form::text('country_name', null,['class' => 'form-control','placeholder'=>trans('countries.country_name'),'required'=>'true'])!!}
                                    @error('country_name')
                                        <div class="help-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group @error('country_code') ? has-error : ''  @enderror">
                                    {{Form::label('code',trans('countries.country_code'))}}
                                    {!!Form::text('country_code', null,['class' => 'form-control','placeholder'=>trans('countries.country_code'),'required'=>'true'])!!}
                                    @error('country_code')
                                        <div class="help-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- <div class="col-md-4">
                                <div class="form-group @error('dial_code') ? has-error : ''  @enderror">
                                    {{Form::label('dial_code', trans('countries.phone_dial_code'))}}
                                    {!!Form::text('dial_code', null,['class' => 'form-control','placeholder'=>trans('countries.phone_dial_code'),'required'=>'true'])!!}
                                    @error('dial_code')
                                        <div class="help-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->
                        
                            <div class="col-md-4">
                                <div class="form-group @error('currency') ? has-error : ''  @enderror">
                                    {{Form::label('currency',trans('countries.currency_name'))}}
                                    {!!Form::text('currency', null,['class' => 'form-control','placeholder'=>trans('countries.currency_name'),'required'=>'true'])!!}
                                    @error('currency')
                                        <div class="help-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group @error('currency_code') ? has-error : ''  @enderror">
                                    {{Form::label('currency_code',trans('countries.currency_code'))}}
                                    {!!Form::text('currency_code', null,['class' => 'form-control','placeholder'=>trans('countries.currency_code'),'required'=>'true'])!!}
                                    @error('currency_code')
                                        <div class="help-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group @error('currency_symbol') ? has-error : ''  @enderror">
                                    {{Form::label('currency_symbol', trans('countries.currency_symbol'))}}
                                    {!!Form::text('currency_symbol', null,['class' => 'form-control','placeholder'=>trans('countries.currency_symbol'),'required'=>'true'])!!}
                                    @error('currency_symbol')
                                        <div class="help-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group @error('status') ? has-error : ''  @enderror">
                                    {{Form::label('status', trans('common.status'))}}
                                    {!! Form::select('status', ['active' => 'Active','inactive' => 'Inactive'],null , ['class' => 'form-control']) !!}
                                    @error('status')
                                        <div class="help-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{ trans('common.submit') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
@section('js')
<script>
    $(document).ready(function () {
    $('#country').validate({ 
        rules: {
            country_name: {
                required: true,
                // lettersonly : true,
                // alphanumeric : true
            },
            country_code: {
                required: true,
                digits: true
            },
            dial_code: {
                required: true,
                digits: true
            },
            currency: {
                required: true,
                // lettersonly : true,
                // alphanumeric : true
            },
            currency_code: {
                required: true,
            },
            currency_symbol: {
                required: true,
            },
            status: {
                required: true,
            },
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        errorPlacement: function (error, element) {
            error.insertAfter($(element));
            $(element).closest('.form-group').addClass('has-error');
        }
    });
});
</script>
@endsection


