@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
.btn {width: 100%}
.info-box {padding: 15px; border: 1px solid #e5e5e5;}
.box-title {padding: 0px 15px 10px;}
.cancel_btn {width: 30%}
.cancel_btn a{background: #fff; color: #dd4b39}
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

.received_status { }
.confirmed_status { background: orange }
.ready_for_pickup_status { background: green }
.picked_up_status { background: green }
.cancelled_status { background: red }

span.label-danger {font-size: 14px;}

.nav-tabs {border: 0px;}
.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
background: #fff;
border: 0px;
font-weight: bold;
color: #337ab7
}
.nav-tabs>li>a, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover{
background: #fff;
border: 0px;
font-weight: bold;
color: #a9a9a9;
font-size:15px;
}

.no_data { text-align: center; margin: 20px; }
a, a:hover { color: inherit; } 


.printtable * { display : none; }
.printtable table { display : none; }
.fa-print {font-size: 20px; cursor: pointer; color: #dd4b39}
.title_detail_link {margin-right: 15px; color: #dd4b39}
</style>
@endsection 
@section('content')
<section class="content-header">
  <h1>
    {{ trans('one_view.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('one_view','reservations')}}">{{ trans('one_view.plural') }}</a></li>
  </ol>
</section>
<br>
<center>
  <div class="form-group row">
    <div class="col-md-3">
    </div>
    @if($reservation_button > 0)
    <div class="col-md-2">
      <a href="{{route('one_view','reservations')}}" class="btn btn-default">{{ trans('one_view.reservation') }}</a>
    </div>
    @endcan

    @if($waiting_list_button > 0)
    <div class="col-md-2">
      <!-- <span class="badge bg-default">3</span> -->
      <a href="{{route('one_view','waiting_list')}}" class="btn btn-default">{{ trans('one_view.waiting_list') }}</a>
    </div>
    @endif

    @if($pickup_button > 0)
    <div class="col-md-2">
      <span class="badge" id="pickups_badge">{{$badge_count}}</span> 
      <a href="{{route('one_view','pickups')}}" class="btn btn-danger">{{ trans('one_view.pick_up') }}</a>
    </div>
    @endif
    
    <div class="col-md-3">
    </div>
  </div>   
</center>
<br>

<section class="content">
<!-- Info boxes -->
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <h3 class="box-title">
            <a class="title_detail_link" href="{{ route('one_view','pickups') }}">
              <i class="fa fa-arrow-left"></i>
            </a>
            {{ trans('one_view.pickup_order_id') }}: {{$pickup->id}}
          </h3>
            <div class="col-md-4 col-sm-6 col-xs-12" id="print-content">
              
                <div class="info-box">
                  <!-- Format Date -->
                    @php 
                    $date = \Carbon\Carbon::parse($pickup->wl_datetime);
                    $pickup->wl_datetime = $date->format('dS M Y h:i A');
                    

                    $classname = '';
                      if($pickup->order_status == 'received'){
                        $classname  = 'received_status';
                      }
                      if($pickup->order_status == 'confirmed'){
                        $classname  = 'confirmed_status';
                      }
                      if($pickup->order_status == 'ready_for_pickup'){
                        $classname  = 'ready_for_pickup_status';
                      }
                      if($pickup->order_status == 'picked_up'){
                        $classname  = 'picked_up_status';
                      }
                      if($pickup->order_status == 'cancelled'){
                        $classname  = 'cancelled_status';
                      }



                     @endphp
                    <table class="table_info">
                        <tr>
                          <td>
                            <b>{{ trans('one_view.date') }}:</b> 
                          </td>
                          <td>{{$pickup->wl_datetime}}</td>
                        </tr>
                        <tr>
                          <td>
                            <b>{{ trans('one_view.name') }}:</b>
                          </td>
                          <td>{{$pickup->first_name}}</td>
                        </tr>
                        <tr>
                          <td>
                            <b>{{ trans('one_view.phone_number') }}:</b>
                          </td>
                          <td>{{$pickup->phone_number}}</td>
                        </tr>
                    </table>
                </div>

                <div class="info-box">
                    <table class="table_info">
                      <tr>
                        <td>
                          <b>{{trans('one_view.ordered')}}</b> 
                        </td>
                        <td></td>
                        <td><i class="fa fa-print" onclick="printDiv('print-content')"></i></td>
                      </tr>
                      @foreach($pickup->pickup_order_items as $o_item)
                      <tr>
                        <td width="50%">
                          <b>{{$o_item->menu_title}}</b>
                        </td>
                        <td width="20%">
                          X {{$o_item->quantity}}
                        </td>
                        <td width="30%">{{\App\Models\Setting::get('currency')}} {{$o_item->unit_price}}</td>
                      </tr>
                      @endforeach
                      <tr>
                        <td colspan="3"><hr></td>
                      </tr>
                      <tr>
                        <td>{{trans('one_view.sub_total')}}</td>
                        <td></td>
                        <td>{{\App\Models\Setting::get('currency')}} {{$pickup->sub_total}}</td>
                      </tr>
                      <tr>
                        <td><b>{{trans('one_view.grand_total')}}</b></td>
                        <td></td>
                        <td><b>{{\App\Models\Setting::get('currency')}} {{$pickup->grand_total}}</b></td>
                      </tr>
                    </table>
                </div>    
                             
                <table class="table_btns">
                  <tr>
                    <td class="cancel_btn">
                      @if($pickup->status != 'cancelled')
                      <a href ="javascript:void(0)" @if($pickup->status == 'cancelled' || $pickup->status != 'in_queue') disabled @endif onclick="return cancel_reservation('{{$pickup->id}}')" class="btn btn-danger">{{ trans('one_view.cancel') }}</a>
                      @endif
                    </td>
                    <td class="btn_space">&nbsp;</td>
                    <td class="status_btn">
                      <select class="btn btn-info status_drpd {{$classname}}" id="{{$pickup->id}}" @if($pickup->order_status == 'cancelled') disabled @endif>
                              <option value="received" 
                                @if($pickup->order_status == 'received') selected @endif>{{trans('pickups.status.received')}}
                              </option>
                              <option value="confirmed" 
                                @if($pickup->order_status == 'confirmed') selected @endif>{{trans('pickups.status.confirmed')}}
                              </option>
                              @if($pickup->order_status == 'cancelled')
                                <option value="cancelled"
                                  @if($pickup->order_status == 'cancelled') selected @endif>{{trans('pickups.status.cancelled')}}
                                </option>
                              @endif
                              <option value="ready_for_pickup"
                                @if($pickup->order_status == 'ready_for_pickup') selected @endif>{{trans('pickups.status.ready_for_pickup')}}
                              </option>
                              <option value="picked_up"
                                @if($pickup->order_status == 'picked_up') selected @endif>{{trans('pickups.status.picked_up')}}
                              </option>
                            </select>
                    </td>
                  </tr>
                </table>

                  
              <!-- /.info-box -->
            </div>
             
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
      // console.log(status);
      $(this).removeClass('received_status');
      $(this).removeClass('confirmed_status');
      $(this).removeClass('ready_for_pickup_status');
      $(this).removeClass('picked_up_status');
      $(this).removeClass('cancelled_status');
      if(status == 'received') {
        $(this).addClass('received_status');
      }
      if(status == 'confirmed') {
        $(this).addClass('confirmed_status');
      }
      if(status == 'ready_for_pickup') {
        $(this).addClass('ready_for_pickup_status');
      }
      if(status == 'picked_up') {
        $(this).addClass('picked_up_status');
      }
      if(status == 'cancelled') {
        $(this).addClass('cancelled_status');
      }

      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('pickup_order_status')}}",
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
            $('#pickups_badge').html(data.badge_count);
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
        url: "{{route('pickup_order_status')}}",
        data: {
                "status": 'cancelled', 
                "id" : reservation_id,  
                "_token": "{{ csrf_token() }}"
        },
        beforeSend: function () {
        },
        success: function (data) {
          $('#pickups_badge').html(data.badge_count);
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

function printDiv(divName) {
        $('.table_btns').hide();
        var printContents = document.getElementById(divName).innerHTML;
        w=window.open();
        w.document.write(printContents);
        w.print();
        $('.table_btns').show();
        w.close();
    }

</script>
@endsection
