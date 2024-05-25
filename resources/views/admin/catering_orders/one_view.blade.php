@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
.btn {width: 100%}
.info-box {padding: 15px; border: 1px solid #e5e5e5;}
.box-title {padding: 0px 15px 10px;}
.cancel_btn {width: 30%}
.cancel_btn a{background: #fff; color: #dd4b39}
.btn_space {width: 30%}
.status_btn {width: 40%}
.table_info {width: 100%}
.table_info tr {line-height: 2}
.table_btns{margin-top: 10px;}

.status_drpd{background: auto}

.badge {
    position: absolute;
    top: -11px;
    right: 26px;
    font-size: 17px;
    font-weight: 400;
}

.pending_status { }
.cancelled_status { background: red }
.booked_status { background: orange }
.completed_status { background: green }
span.label-danger {font-size: 14px;}
.no_data { text-align: center; margin: 20px; }
a, a:hover { color: inherit; } 
</style>
@endsection 
@section('content')
<section class="content-header">
  <h1>
    {{ trans('catering_orders.one_view') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('one_view','reservations')}}">{{ trans('one_view.plural') }}</a></li>
  </ol>
</section>


<section class="content">
<!-- Info boxes -->
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <h3 class="box-title">{{ trans('one_view.upcoming_orders') }}</h3>
          @if(count($catering_orders))
          <div class="row">
            @foreach($catering_orders as $res)
            
              <div class="col-md-4 col-sm-6 col-xs-12">
                
                  <div class="info-box">
                    <!-- Format Date -->
                      @php 
                      $date = \Carbon\Carbon::parse($res->check_in_date);
                      $res->check_in_date = $date->format('dS M Y h:i A');
                      

                      $classname = '';
                      if($res->order_status == 'pending'){
                        $classname  = 'pending_status';
                      }
                      if($res->order_status == 'booked'){
                        $classname  = 'booked_status';
                      }
                      if($res->order_status == 'cancelled'){
                        $classname  = 'cancelled_status';
                      }
                      if($res->order_status == 'completed'){
                        $classname  = 'completed_status';
                      }



                       @endphp
                      <a href="{{route('catering_orders.show', $res->id)}}">
                        <table class="table_info">
                              <tr>
                                <td style="width: 45%">
                                  <b>{{ trans('one_view.catering_order_id') }}:</b>
                                </td>
                                <td>{{$res->id}}</td>
                              </tr>
                              <tr>
                                <td>
                                  <b>{{ trans('one_view.date') }}:</b> 
                                </td>
                                <td>{{$res->check_in_date}}</td>
                              </tr>
                              <tr>
                                <td>
                                  <b>{{ trans('one_view.name') }}:</b>
                                </td>
                                <td>{{$res->first_name}}</td>
                              </tr>
                              <tr>
                                <td>
                                  <b>{{ trans('one_view.phone_number') }}:</b>
                                </td>
                                <td>{{$res->phone_number}}</td>
                              </tr>
                              <tr>
                                <td>
                                  <b>{{ trans('one_view.address') }}:</b>
                                </td>
                                <td>{{@$res->order_address->apartment_number.' '.@$res->order_address->floor.' '.@$res->order_address->house_bulding.' '.@$res->order_address->avenue.' '.@$res->order_address->street.' '.@$res->order_address->block.' '. @$res->order_address->city->name}}</td>
                              </tr>
                              
                        </table>
                      </a>
                      
                          
                      <table class="table_btns">
                        <tr>
                          <td class="cancel_btn">
                            @if($res->status != 'cancelled')
                            <a href ="javascript:void(0)" @if($res->status == 'cancelled' || $res->status != 'confirmed') disabled @endif onclick="return cancel_reservation('{{$res->id}}')" class="btn btn-danger">{{ trans('one_view.cancel') }}</a>
                            @endif
                          </td>
                          <td class="btn_space">&nbsp;</td>
                          <td class="status_btn">
                            <select class="btn btn-info status_drpd {{$classname}}" id="{{$res->id}}" @if($res->order_status == 'cancelled') disabled @endif>
                              @if($res->order_status != 'completed')
                              <option value="booked" 
                                @if($res->order_status == 'booked') selected @endif>{{trans('catering_orders.status.booked')}}
                              </option>
                              @endif
                              @if($res->order_status == 'cancelled')
                                <option value="cancelled"
                                  @if($res->order_status == 'cancelled') selected @endif>{{trans('catering_orders.status.cancelled')}}
                                </option>
                              @endif
                              <option value="completed"
                                @if($res->order_status == 'completed') selected @endif>{{trans('catering_orders.status.completed')}}
                              </option>
                            </select>
                          </td>
                        </tr>
                      </table>
                  </div>
                <!-- /.info-box -->
              </div>
            @endforeach
          </div>
          {{$catering_orders->links()}}
          @else
            <p class="no_data">{{trans('common.no_data')}}</p>
          @endif  
        </div>
      </div>
    </div>
  </div>

</section>

@endsection 

@section('js')
<script>
$(document).on('change','.status_drpd',function(){
      var status = $(this).val();
      console.log(status);
      $(this).removeClass('pending_status');
      $(this).removeClass('cancelled_status');
      $(this).removeClass('booked_status');
      $(this).removeClass('completed_status');
      if(status == 'pending') {
        $(this).addClass('pending_status');
      }
      if(status == 'cancelled') {
        $(this).addClass('cancelled_status');
      }
      if(status == 'booked') {
        $(this).addClass('booked_status');
      }
      if(status == 'completed') {
        $(this).addClass('completed_status');
      }
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('catering_order_status')}}",
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
            $('#reservations_badge').html(data.badge_count);
            setTimeout(function() {
                  element.next('.loading').css('visibility', 'hidden');
              }, delay);
            toastr.success(data.success);
          },
          error: function () {
            toastr.error(data.error);
          }
      })
  })

function cancel_reservation(reservation_id) {
    if(confirm("{{trans('common.confirm_cancel')}}")){
      $.ajax({
        type:'post',
        url: "{{route('catering_order_status')}}",
        data: {
                "status": 'cancelled', 
                "id" : reservation_id,  
                "_token": "{{ csrf_token() }}"
        },
        beforeSend: function () {
        },
        success: function (data) {
          $('#reservations_badge').html(data.badge_count);
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
