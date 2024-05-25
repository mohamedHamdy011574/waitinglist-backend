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
.table_btns{margin-top: 10px; width: 100%}

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
.fa-print {font-size: 20px; cursor: pointer; color:#dd4b39 }
.title_detail_link {margin-right: 15px; color: #dd4b39}

</style>
@endsection 
@section('content')
<section class="content-header">
  <h1>
    {{ trans('catering_orders.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{ URL::previous() }}">{{ trans('catering_orders.heading') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
          <div class="box-header">
            <h3 class="box-title"> 
              <a class="title_detail_link" href="{{ URL::previous() }}">
              <i class="fa fa-arrow-left"></i>
              </a>
            {{ trans('catering_orders.order_id') }}: {{$detail->id}}</h3>
          </div>
          <div class="box-body">
            <div class="col-md-5 col-sm-8 col-xs-12" >
              <div class="info-box">

                <!-- Format Date -->
                @php $date = \Carbon\Carbon::parse($detail->wl_datetime);
                $detail->wl_datetime = $date->format('dS M Y h:i A'); 

                $classname = '';
                if($detail->order_status == 'pending'){
                  $classname  = 'pending_status';
                }
                if($detail->order_status == 'booked'){
                  $classname  = 'booked_status';
                }
                if($detail->order_status == 'cancelled'){
                  $classname  = 'cancelled_status';
                }
                if($detail->order_status == 'completed'){
                  $classname  = 'completed_status';
                }

                @endphp

                 <table class="table_info">
                  
                  <tr>
                    <td>
                      <b>{{ trans('catering_orders.date') }}:</b> 
                    </td>
                    <td>{{$detail->wl_datetime}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('catering_orders.customer') }}:</b>
                    </td>
                    <td>{{$detail->first_name}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('catering_orders.contact') }}:</b>
                    </td>
                    <td>{{$detail->phone_number}}</td>
                  </tr>
                  <tr>
                    <td>
                      <b>{{ trans('one_view.address') }}:</b>
                    </td>
                    <td>{{@$detail->order_address->apartment_number.' '.@$detail->order_address->floor.' '.@$detail->order_address->house_bulding.' '.@$detail->order_address->avenue.' '.@$detail->order_address->street.' '.@$detail->order_address->block.' '. @$detail->order_address->city->name}}
                    </td>
                  </tr>
                  
                </table>
              </div>

              <div class="info-box" id="print-content">
                <table class="table_info">
                  <tr>
                    <td>
                      <b>{{trans('one_view.ordered')}}</b> 
                    </td>
                    <td></td>
                    <td><i class="fa fa-print" onclick="printDiv('print-content')"></i></td>
                  </tr>
                  @foreach($detail->catering_order_items as $co_item)
                    <tr>
                      <td width="50%">
                        <b>{{$co_item->catering_package->package_name}}</b>
                      </td>
                      <td width="20%">
                        X {{$co_item->quantity}}
                      </td>
                      <td width="30%">{{\App\Models\Setting::get('currency')}} {{$co_item->unit_price}}</td>
                    </tr>

                    @if($co_item->catering_addon_order)

                      @foreach($co_item->catering_addon_order->catering_addon_order_items as $addon_order_item)
                      <tr>
                        <td width="50%">{{$addon_order_item->catering_addon->addon_name}}</td>
                        <td width="20%">X {{$addon_order_item->quantity}}</td>
                        <td width="30%">{{$addon_order_item->unit_price}}</td>
                      </tr>
                      @endforeach
                    @endif
                  
                  
                  @endforeach
                  <tr>
                    <td colspan="3"><hr></td>
                  </tr>
                  <tr>
                    <td>{{trans('one_view.sub_total')}}</td>
                    <td></td>
                    <td>{{\App\Models\Setting::get('currency')}} {{$detail->sub_total}}</td>
                  </tr>
                  <tr>
                    <td>{{trans('one_view.addons_total')}}</td>
                    <td></td>
                    <td>{{\App\Models\Setting::get('currency')}} {{$detail->addons_total}}</td>
                  </tr>

                  <tr>
                    <td>{{trans('one_view.grand_total')}}</td>
                    <td></td>
                    <td>{{\App\Models\Setting::get('currency')}} {{$detail->grand_total}}</td>
                  </tr>
                </table>
                </div> 
                  <table class="table_btns">
                  <tr>
                    <td class="cancel_btn">
                      @if($detail->status != 'cancelled')
                      <a href ="javascript:void(0)" @if($detail->status == 'cancelled' || $detail->status != 'completed') disabled @endif onclick="return cancel_reservation('{{$detail->id}}')" class="btn btn-danger">{{ trans('one_view.cancel') }}</a>
                      @endif
                    </td>
                    <td class="btn_space">&nbsp;</td>
                    <td class="status_btn">
                      <select class="btn btn-info status_drpd {{$classname}}" id="{{$detail->id}}" @if($detail->order_status == 'cancelled') disabled @endif>
                              <option value="pending" 
                                @if($detail->order_status == 'pending') selected @endif>{{trans('catering_orders.status.pending')}}
                              </option>
                              <option value="booked" 
                                @if($detail->order_status == 'booked') selected @endif>{{trans('catering_orders.status.booked')}}
                              </option>
                              @if($detail->order_status == 'cancelled')
                                <option value="cancelled"
                                  @if($detail->order_status == 'cancelled') selected @endif>{{trans('catering_orders.status.cancelled')}}
                                </option>
                              @endif
                              <option value="completed"
                                @if($detail->order_status == 'completed') selected @endif>{{trans('catering_orders.status.completed')}}
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
      // console.log(status);
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
        url: "{{route('catering_order_status')}}",
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