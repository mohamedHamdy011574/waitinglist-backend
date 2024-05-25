@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
  .btn {width: 100%}
.info-box {padding: 15px; border: 1px solid #e5e5e5;}
.box-title {padding: 0px 15px 10px;}
.cancel_btn {width: 30%}
.cancel_btn a{background: #fff; color: #367fa9}
.btn_space {width: 20%}
.status_btn {width: 50%}
.table_info {width: 100%}
.table_info tr {line-height: 2}
.table_btns{margin-top: 10px; width: 100%}

.status_drpd{background: auto}

.badge {
    position: absolute;
    top: -11px;
    right: 26px;
    font-size: 17px;
    font-weight: 400;
}

.in_queue_status { }
.cancelled_status { background: red }
.checked_in_status { background: orange }
.checked_out_status { background: green }
span.label-danger {font-size: 14px;}
.no_data { text-align: center; margin: 20px; }
a, a:hover { color: inherit; } 
</style>
@endsection 
@section('content')
<section class="content-header">
  <h1>
    {{ trans('waiting_list.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('waiting_list.index')}}">{{ trans('waiting_list.w_title') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
          <div class="box-header">
            <h3 class="box-title"> 
              <a class="title_detail_link" href="{{ route('waiting_list.index') }}">
              <i class="fa fa-arrow-left"></i>
              </a>
            {{ trans('one_view.token_no') }}: {{$detail->token_number}}
          </div>
          <div class="box-body">
            <div class="col-md-4 col-sm-8 col-xs-12" >
              <div class="info-box">

                <!-- Format Date -->
                @php $date = \Carbon\Carbon::parse($detail->wl_datetime);
                $detail->wl_datetime = $date->format('dS M Y h:i A'); 

                $classname = '';

                if($detail->status == 'in_queue'){
                  $classname  = 'in_queue_status';
                }
                if($detail->status == 'cancelled'){
                  $classname  = 'cancelled_status';
                }
                if($detail->status == 'checked_in'){
                  $classname  = 'checked_in_status';
                }
                if($detail->status == 'checked_out'){
                  $classname  = 'checked_out_status';
                }

                @endphp

                 <table class="table_info">
                  <tr>
                    <td style="width: 45%">
                      <b>{{ trans('waiting_list.id') }}:</b>
                    </td>
                    <td>{{$detail->id}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('waiting_list.date') }}:</b> 
                    </td>
                    <td>{{$detail->wl_datetime}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('waiting_list.customer') }}:</b>
                    </td>
                    <td>{{$detail->first_name}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('waiting_list.contact') }}:</b>
                    </td>
                    <td>{{$detail->phone_number}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('waiting_list.chairs') }}:</b>
                    </td>
                    <td><span class="label label-danger">{{$detail->reserved_chairs}}</span> </td>
                  </tr>
                </table>
                 <table class="table_btns">
                  @if($detail->customer_id && $detail->wl_date == date('Y-m-d') && $detail->status == 'in_queue')
                    <tr>
                      <td colspan="3">
                        <a href ="javascript:void(0)" class="btn btn-primary notifyModal" data-id="{{$detail->id}}">{{ trans('waiting_list.notification') }}</a><br/><br/>
                      </td>
                    </tr>
                  @endif
                      <tr>
                        <td class="cancel_btn">
                          <a href ="javascript:void(0)" @if($detail->status == 'cancelled' || $detail->status != 'in_queue') disabled @endif onclick="return cancel_reservation('{{$detail->id}}')" class="btn btn-primary">{{ trans('waiting_list.cancel') }}</a>
                        </td>
                        <td class="btn_space">&nbsp;</td>
                        <td class="status_btn">
                          <select class="btn btn-info status_drpd {{$classname}}" id="{{$detail->id}}" @if($detail->status == 'cancelled') disabled @endif>
                            <option value="in_queue" 
                              @if($detail->status == 'in_queue') selected @endif>{{trans('waiting_list.status.in_queue')}}
                            </option>
                            @if($detail->status == 'cancelled')
                              <option value="cancelled"
                                @if($detail->status == 'cancelled') selected @endif>{{trans('waiting_list.status.cancelled')}}
                              </option>
                            @endif
                            <option value="checked_in"
                              @if($detail->status == 'checked_in') selected @endif>{{trans('waiting_list.status.checked_in')}}
                            </option>
                            <option value="checked_out"
                              @if($detail->status == 'checked_out') selected @endif>{{trans('waiting_list.status.checked_out')}}
                            </option>
                          </select>
                        </td>
                      </tr>
                    </table>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-labelledby="NotifyLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ trans('waiting_list.notification') }}</h4>
      </div>
      <div class="modal-body">
        <form class="form-group" method="PUT" id="notifyForm">
          <div class="row">
            <input class="form-control" id="notify_id" required="true" name="notify_id" type="hidden">
            
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">{{ trans('waiting_list.title') }}</label>
                  <input class="form-control" placeholder="Enter Title" required="true" name="title" type="text" id="title">
                 
                </div>
              </div>
          </div>
           
          <div class="row">

            <div class="col-md-12">
              <div class="form-group">
                <label for="name">{{ trans('waiting_list.message') }}</label>
                <textarea class="form-control" placeholder="Enter Message" required="true" name="body" id="content" rows="3"> </textarea>
              </div>
            </div>

          </div>
        </form>
       
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" type="button" id="send_notification">Send</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection 
@section('js')

<script type="text/javascript">

  $(document).on('click','.notifyModal', function(){
      var waiting_list_id = $(this).data('id');
      var title = "{{trans('waiting_list.table_ready')}}";
      var body = "{{trans('waiting_list.table_ready_detail')}}";
      $.ajax({
            type:'post',
            url: "{{ route('wl_send_notification') }}",
            data: {
                    "id"         : waiting_list_id, 
                    "title"      : title,
                    "body"       : body,  
                    "_token": "{{ csrf_token() }}"
                  },
            beforeSend: function () {
                $('.notifyModal').html("{{trans('waiting_list.sending')}}");
            },
            success: function (data) {
              if(data.type == 'error'){
                toastr.error(data.message);
              }else{
                $('.notifyModal').html("{{trans('waiting_list.sent')}}");
                // $('#notify').modal('hide');
                toastr.success(data.message);
              }
            },

            error: function () {
              $('#send_notification').html('Send');
              $('#loader').css('visibility', 'hidden');
              toastr.error('Something went wrong');
            }
        })
    })

   $('#send_notification').click(function(){

      var validator = $('#notifyForm').validate({ 
        rules: {
            notify_id: {
                required: true
            },
            title: {
                required: true
            },
            body: {
                required: true
            }
        },
        messages:{
          title:{
            required:'Please enter a title'
          },
          body:{
            required:"Message can't be blank",
          }
        }
      });
      
      validator.form();
     
      if (validator.valid()) 
        {
      
        }
      else 
        {
            return false;
        }
    
        var title        = $('#title').val();
        var body         = $('#content').val();
        var notify_id    = $('#notify_id').val();
     
        $.ajax({
            type:'post',
            url: "{{ route('wl_send_notification') }}",
            data: {
                    "id"         : notify_id, 
                    "title"      : title,
                    "body"       : body,  
                    "_token": "{{ csrf_token() }}"
                  },
            beforeSend: function () {
                $('#send_notification').html('Sending..<span id="loader" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">a</span></span>');
                $('#loader').css('visibility', 'visible');
            },
            success: function (data) {
              $('#send_notification').html('Send');
              $('#loader').css('visibility', 'hidden');

              if(data.type == 'error'){

                toastr.error(data.message);
              }else{

                $('#notify').modal('hide');
                toastr.success(data.message);
              }
            },

            error: function () {
              $('#send_notification').html('Send');
              $('#loader').css('visibility', 'hidden');
              toastr.error('Something went wrong');
            }
        })
    });

   $(document).on('change','.status_drpd',function(){
      var status = $(this).val();
      console.log(status);
      $(this).removeClass('in_queue_status');
      // $(this).removeClass('cancelled_status');
      $(this).removeClass('checked_in_status');
      $(this).removeClass('checked_out_status');
      if(status == 'in_queue') {
        $(this).addClass('in_queue_status');
      }
      if(status == 'cancelled') {
        // $(this).addClass('cancelled_status');
      }
      if(status == 'checked_in') {
        $(this).addClass('checked_in_status');
      }
      if(status == 'checked_out') {
        $(this).addClass('checked_out_status');
      }
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('waiting_list_status')}}",
          data: {
                  "status": status, 
                  "id" : id,  
                  "_token": "{{ csrf_token() }}"
                },
          beforeSend: function () {
              element.next('.loading').css('visibility', 'visible');
          },
          success: function (data) {
            $('#'+data.id).blur();
            $('#waiting_list_badge').html(data.badge_count);
            setTimeout(function() {
                  element.next('.loading').css('visibility', 'hidden');
              }, delay);
            toastr.success(data.success);
          },
          error: function () {
            toastr.error(data.error);
          }
      })
  });

  function cancel_reservation(wlist_id) {
    if(confirm("{{trans('common.confirm_cancel')}}")){
      $.ajax({
        type:'post',
        url: "{{route('waiting_list_status')}}",
        data: {
                "status": 'cancelled', 
                "id" : wlist_id,  
                "_token": "{{ csrf_token() }}"
        },
        beforeSend: function () {
        },
        success: function (data) {
          $('#waiting_list_badge').html(data.badge_count);
          toastr.success(data.success);
          window.location.reload();
        },
        error: function () {
          toastr.error(data.error);
        }
    })
      return true;
    }else{
      return false;
    }
  } 
</script>
@endsection
