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
.status_btn {width: 50%;}
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

.confirmed_status { }
.cancelled_status { background: red }
.checked_in_status { background: orange }
.checked_out_status { background: green }
span.label-info {font-size: 14px;}

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
.fa-print {font-size: 20px; cursor: pointer;}
</style>
@endsection 
@section('content')
<section class="content-header">
  <h1>
    {{ trans('pickups.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('pickup_orders.index')}}">{{ trans('pickups.heading') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
          <div class="box-header">
            <h3 class="box-title"> 
              <a class="title_detail_link" href="{{ route('pickup_orders.index') }}">
              <i class="fa fa-arrow-left"></i>
              </a>
            {{ trans('pickups.order_id') }}: {{$detail->id}}</h3>
          </div>
          <div class="box-body">
            <div class="col-md-4 col-sm-8 col-xs-12" >
              <div class="info-box">

                <!-- Format Date -->
                @php $date = \Carbon\Carbon::parse($detail->wl_datetime);
                $detail->wl_datetime = $date->format('dS M Y h:i A'); 

                $classname = '';
                if($detail->order_status == 'received'){
                  $classname  = 'received_status';
                }
                if($detail->order_status == 'confirmed'){
                  $classname  = 'confirmed_status';
                }
                if($detail->order_status == 'ready_for_pickup'){
                  $classname  = 'ready_for_pickup_status';
                }
                if($detail->order_status == 'picked_up'){
                  $classname  = 'picked_up_status';
                }

                @endphp

                 <table class="table_info">
                  
                  <tr>
                    <td>
                      <b>{{ trans('pickups.date') }}:</b> 
                    </td>
                    <td>{{$detail->wl_datetime}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('pickups.customer') }}:</b>
                    </td>
                    <td>{{$detail->first_name}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('pickups.contact') }}:</b>
                    </td>
                    <td>{{$detail->phone_number}}</td>
                  </tr>
                  
                </table>
              </div>

               <div class="info-box" id="print-content">
                    <table class="table_info">
                      <tr>
                        <td>
                          <b>Ordered</b> 
                        </td>
                        <td></td>
                        <td><i class="fa fa-print" onclick="printDiv('print-content')"></i></td>
                      </tr>
                      @foreach($detail->pickup_order_items as $o_item)
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
                        <td>Sub total</td>
                        <td></td>
                        <td>{{\App\Models\Setting::get('currency')}} {{$detail->sub_total}}</td>
                      </tr>
                      <tr>
                        <td>Grand total</td>
                        <td></td>
                        <td>{{\App\Models\Setting::get('currency')}} {{$detail->grand_total}}</td>
                      </tr>
                    </table>
                </div> 
                  <table class="table_btns">
                  <tr>
                    <td class="cancel_btn">
                      <a href ="javascript:void(0)" @if($detail->status == 'cancelled' || $detail->status != 'in_queue') disabled @endif onclick="return cancel_reservation('{{$detail->id}}')" class="btn btn-primary">{{ trans('one_view.cancel') }}</a>
                    </td>
                    <td class="btn_space">&nbsp;</td>
                    <td class="status_btn">
                      <select class="btn btn-info status_drpd {{$classname}}" id="{{$detail->id}}" @if($detail->order_status == 'cancelled') disabled @endif>
                              <option value="received" 
                                @if($detail->order_status == 'received') selected @endif>{{trans('pickups.status.received')}}
                              </option>
                              <option value="confirmed" 
                                @if($detail->order_status == 'confirmed') selected @endif>{{trans('pickups.status.confirmed')}}
                              </option>
                              @if($detail->order_status == 'cancelled')
                                <option value="cancelled"
                                  @if($detail->order_status == 'cancelled') selected @endif>{{trans('pickups.status.cancelled')}}
                                </option>
                              @endif
                              <option value="ready_for_pickup"
                                @if($detail->order_status == 'ready_for_pickup') selected @endif>{{trans('pickups.status.ready_for_pickup')}}
                              </option>
                              <option value="picked_up"
                                @if($detail->order_status == 'picked_up') selected @endif>{{trans('pickups.status.picked_up')}}
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



@endsection 
@section('js')
<script>
$(document).on('change','.status_drpd',function(){
      var status = $(this).val();
      console.log(status);
      $(this).removeClass('confirmed_status');
      // $(this).removeClass('cancelled_status');
      $(this).removeClass('checked_in_status');
      $(this).removeClass('checked_out_status');
      if(status == 'confirmed') {
        $(this).addClass('confirmed_status');
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