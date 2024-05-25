@extends('layouts.admin')
@section('content')
<section class="content-header">
  <h1>
    {{trans('notifications.create_notification')}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="{{route('notifications.index')}}">{{trans('notifications.notification')}}</a></li>
    <li class="active">{{trans('notifications.create_notification')}}</li>
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
      <form method="POST" enctype="multipart/form-data">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{trans('notifications.notification')}}</h3>
          </div>
          <div class="box-body">
              @csrf
              <!-- <div class="col-md-6">
                <div class="form-group">
                  <label>{{(trans('notifications.title'))}}</label>
                  <input class="form-control" type="text" value="" name="title">
                </div>
              </div> -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{(trans('notifications.noti_title'))}}</label>
                  <input class="form-control" type="text" value="{{old('title')}}" required name="title">
                </div>
                <div class="form-group">
                  <label>{{(trans('notifications.select_notification_type'))}}</label>
                  <select class="form-control" name="message_type">
                    <!-- <option value="">{{(trans('notifications.select_type'))}}</option> -->
                    <!-- <option value="email">{{(trans('notifications.send_email'))}}</option>
                    <option value="messages">{{(trans('notifications.send_message'))}}</option> -->
                    <option value="notification">{{(trans('notifications.send_notification'))}}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>{{(trans('notifications.notification_message'))}}</label>
                  <textarea name="message" class="form-control" rows="3" required>{{old('message')}}</textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{(trans('notifications.select_customer'))}}</label>
                  <select multiple="multiple" id="multiselect1" name="customer_select[]" class="form-control multiselect1" size="7">
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endforeach  
                  </select>
                </div>
              </div>
          </div>
          <div class="box-footer">
            <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('notifications.submit')}}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
@section('js')
<script>
  $(document).ready(function () {
    $('#setting').validate({ 
        rules: {
            name: {
                required: true
            },
            value: {
                required: true,
            },
            status: {
                required: true,
            },
        },
        messages: {
        }
    });
  });

  $('.multiselect-input').attr('autocomplete','off');

  $('.multiselect-text:first').text('{{trans("common.all")}}');
</script>
@endsection
