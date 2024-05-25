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
.table_btns{margin-top: 10px;}

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
color: #dd4b39
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
          
          <ul class="nav nav-tabs">
            
              <li class="active">
                <a href="#upcoming" aria-controls="" role="tab" data-toggle="tab" aria-expanded="true">
                  {{trans('one_view.upcoming_orders')}}
                </a>
              </li> 
              <li class="">
                <a href="#in_progress" aria-controls="" role="tab" data-toggle="tab" aria-expanded="true">
                  {{trans('one_view.in_progress')}}
                </a>
              </li> 
              <li class="">
                <a href="#ready_for_pickup" aria-controls="" role="tab" data-toggle="tab" aria-expanded="true">
                  {{trans('one_view.ready_for_pickup')}}
                </a>
              </li>  
          </ul>
             
          <div class="tab-content">
            <div class="tab-pane fade in active" id="upcoming">
              @if(count($upcoming_pickups))
              <div class="row">
                @foreach($upcoming_pickups as $up_p)
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <!-- Format Date -->
                      @php 
                      $date = \Carbon\Carbon::parse($up_p->pickup_date);
                      $up_p->pickup_date = $date->format('dS M Y h:i A');
                      

                      $classname = '';
                      if($up_p->order_status == 'received'){
                        $classname  = 'received_status';
                      }
                      if($up_p->order_status == 'confirmed'){
                        $classname  = 'confirmed_status';
                      }
                      if($up_p->order_status == 'ready_for_pickup'){
                        $classname  = 'ready_for_pickup_status';
                      }
                      if($up_p->order_status == 'picked_up'){
                        $classname  = 'picked_up_status';
                      }
                      if($up_p->order_status == 'cancelled'){
                        $classname  = 'cancelled_status';
                      }



                      @endphp
                      <a href="{{route('one_view_detail', ['pickups', $up_p->id])}}">
                        <table class="table_info">
                          <tr>
                            <td style="width: 45%">
                              <b>{{ trans('one_view.pickup_order_id') }}:</b>
                            </td>
                            <td>{{$up_p->id}}</td>
                          </tr>
                          <tr>
                            <td>
                              <b>{{ trans('one_view.date') }}:</b> 
                            </td>
                            <td>{{$up_p->pickup_date}}</td>
                          </tr>
                          <tr>
                            <td>
                              <b>{{ trans('one_view.name') }}:</b>
                            </td>
                            <td>{{$up_p->first_name}}</td>
                          </tr>
                          <tr>
                            <td>
                              <b>{{ trans('one_view.phone_number') }}:</b>
                            </td>
                            <td>{{$up_p->phone_number}}</td>
                          </tr>
                        </table>
                      </a>
                          
                      <table class="table_btns">
                        <tr>
                          <td class="cancel_btn">
                            @if($up_p->order_status != 'cancelled')
                            <a href ="javascript:void(0)" @if($up_p->order_status == 'cancelled' || $up_p->order_status != 'confirmed') disabled @endif onclick="return cancel_reservation('{{$up_p->id}}')" class="btn btn-danger">{{ trans('one_view.cancel') }}</a>
                            @endif
                          </td>
                          <td class="btn_space" style="width: 20%">&nbsp;</td>
                          <td class="status_btn" style="width: 50%">
                            <select class="btn btn-info status_drpd {{$classname}}" id="{{$up_p->id}}" @if($up_p->order_status == 'cancelled') disabled @endif>
                              <option value="received" 
                                @if($up_p->order_status == 'received') selected @endif>{{trans('pickups.status.received')}}
                              </option>
                              <option value="confirmed" 
                                @if($up_p->order_status == 'confirmed') selected @endif>{{trans('pickups.status.confirmed')}}
                              </option>
                              @if($up_p->order_status == 'cancelled')
                                <option value="cancelled"
                                  @if($up_p->order_status == 'cancelled') selected @endif>{{trans('pickups.status.cancelled')}}
                                </option>
                              @endif
                              <option value="ready_for_pickup"
                                @if($up_p->order_status == 'ready_for_pickup') selected @endif>{{trans('pickups.status.ready_for_pickup')}}
                              </option>
                              <option value="picked_up"
                                @if($up_p->order_status == 'picked_up') selected @endif>{{trans('pickups.status.picked_up')}}
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
              
              {{$upcoming_pickups->links()}}
              @else
                <p class="no_data">{{trans('common.no_data')}}</p>
              @endif  
              
            </div>

            <div class="tab-pane fade" id="in_progress">
              @if(count($pickups_in_progress))
                @foreach($pickups_in_progress as $ip_p)
                <div class="col-md-4 col-sm-6 col-xs-12">
                  
                    <div class="info-box">
                      <!-- Format Date -->
                        @php 
                        $date = \Carbon\Carbon::parse($ip_p->pickup_date);
                        $ip_p->pickup_date = $date->format('dS M Y h:i A');
                        

                        $classname = '';
                        if($ip_p->order_status == 'received'){
                        $classname  = 'received_status';
                        }
                        if($ip_p->order_status == 'confirmed'){
                          $classname  = 'confirmed_status';
                        }
                        if($ip_p->order_status == 'ready_for_pickup'){
                          $classname  = 'ready_for_pickup_status';
                        }
                        if($ip_p->order_status == 'picked_up'){
                          $classname  = 'picked_up_status';
                        }
                        if($ip_p->order_status == 'cancelled'){
                          $classname  = 'cancelled_status';
                        }



                        @endphp
                        <a href="{{route('one_view_detail', ['pickups', $ip_p->id])}}">
                          <table class="table_info">
                                  <tr>
                                    <td style="width: 45%">
                                      <b>{{ trans('one_view.pickup_order_id') }}:</b>
                                    </td>
                                    <td>{{$ip_p->id}}</td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <b>{{ trans('one_view.date') }}:</b> 
                                    </td>
                                    <td>{{$ip_p->pickup_date}}</td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <b>{{ trans('one_view.name') }}:</b>
                                    </td>
                                    <td>{{$ip_p->first_name}}</td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <b>{{ trans('one_view.phone_number') }}:</b>
                                    </td>
                                    <td>{{$ip_p->phone_number}}</td>
                                  </tr>
                          </table>
                        </a>  
                            
                        <table class="table_btns">
                          <tr>
                            <td class="cancel_btn">
                              @if($ip_p->order_status != 'cancelled')
                              <a href ="javascript:void(0)" @if($ip_p->order_status == 'cancelled' || $ip_p->order_status != 'confirmed') disabled @endif onclick="return cancel_reservation('{{$ip_p->id}}')" class="btn btn-danger">{{ trans('one_view.cancel') }}</a>
                              @endif
                            </td>
                            <td class="btn_space" style="width: 20%">&nbsp;</td>
                          <td class="status_btn" style="width: 50%">
                            <select class="btn btn-info status_drpd {{$classname}}" id="{{$ip_p->id}}" @if($ip_p->order_status == 'cancelled') disabled @endif>
                              <option value="received" 
                                @if($ip_p->order_status == 'received') selected @endif>{{trans('pickups.status.received')}}
                              </option>
                              <option value="confirmed" 
                                @if($ip_p->order_status == 'confirmed') selected @endif>{{trans('pickups.status.confirmed')}}
                              </option>
                              @if($ip_p->order_status == 'cancelled')
                                <option value="cancelled"
                                  @if($ip_p->order_status == 'cancelled') selected @endif>{{trans('pickups.status.cancelled')}}
                                </option>
                              @endif
                              <option value="ready_for_pickup"
                                @if($ip_p->order_status == 'ready_for_pickup') selected @endif>{{trans('pickups.status.ready_for_pickup')}}
                              </option>
                              <option value="picked_up"
                                @if($ip_p->order_status == 'picked_up') selected @endif>{{trans('pickups.status.picked_up')}}
                              </option>
                            </select>
                          </td>
                          </tr>
                        </table>
                    </div>
                  <!-- /.info-box -->
                </div>
                @endforeach
              @else
                <p class="no_data">{{trans('common.no_data')}}</p>
              @endif
              
            </div>

            <div class="tab-pane fade" id="ready_for_pickup">
              @if(count($pickups_ready))
                @foreach($pickups_ready as $rd_p)
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <!-- Format Date -->
                      @php 
                      $date = \Carbon\Carbon::parse($rd_p->pickup_date);
                      $rd_p->pickup_date = $date->format('dS M Y h:i A');
                      

                      $classname = '';
                      if($rd_p->order_status == 'received'){
                      $classname  = 'received_status';
                      }
                      if($rd_p->order_status == 'confirmed'){
                        $classname  = 'confirmed_status';
                      }
                      if($rd_p->order_status == 'ready_for_pickup'){
                        $classname  = 'ready_for_pickup_status';
                      }
                      if($rd_p->order_status == 'picked_up'){
                        $classname  = 'picked_up_status';
                      }
                      if($rd_p->order_status == 'cancelled'){
                        $classname  = 'cancelled_status';
                      }



                       @endphp
                      <a href="{{route('one_view_detail', ['pickups', $rd_p->id])}}"> 
                        <table class="table_info">
                          <tr>
                            <td style="width: 45%">
                              <b>{{ trans('one_view.pickup_order_id') }}:</b>
                            </td>
                            <td>{{$rd_p->id}}</td>
                          </tr>
                          <tr>
                            <td>
                              <b>{{ trans('one_view.date') }}:</b> 
                            </td>
                            <td>{{$rd_p->pickup_date}}</td>
                          </tr>
                          <tr>
                            <td>
                              <b>{{ trans('one_view.name') }}:</b>
                            </td>
                            <td>{{$rd_p->first_name}}</td>
                          </tr>
                          <tr>
                            <td>
                              <b>{{ trans('one_view.phone_number') }}:</b>
                            </td>
                            <td>{{$rd_p->phone_number}}</td>
                          </tr>
                        </table>
                      </a>
                          
                      <table class="table_btns">
                        <tr>
                          <td class="cancel_btn">
                            @if($rd_p->order_status != 'cancelled')
                            <a href ="javascript:void(0)" @if($rd_p->order_status == 'cancelled' || $rd_p->order_status != 'confirmed') disabled @endif onclick="return cancel_reservation('{{$rd_p->id}}')" class="btn btn-danger">{{ trans('one_view.cancel') }}</a>
                            @endif
                          </td>
                          <td class="btn_space" style="width: 20%">&nbsp;</td>
                          <td class="status_btn" style="width: 50%">
                            <select class="btn btn-info status_drpd {{$classname}}" id="{{$rd_p->id}}" @if($rd_p->order_status == 'cancelled') disabled @endif>
                              <option value="received" 
                                @if($rd_p->order_status == 'received') selected @endif>{{trans('pickups.status.received')}}
                              </option>
                              <option value="confirmed" 
                                @if($rd_p->order_status == 'confirmed') selected @endif>{{trans('pickups.status.confirmed')}}
                              </option>
                              @if($rd_p->order_status == 'cancelled')
                                <option value="cancelled"
                                  @if($rd_p->order_status == 'cancelled') selected @endif>{{trans('pickups.status.cancelled')}}
                                </option>
                              @endif
                              <option value="ready_for_pickup"
                                @if($rd_p->order_status == 'ready_for_pickup') selected @endif>{{trans('pickups.status.ready_for_pickup')}}
                              </option>
                              <option value="picked_up"
                                @if($rd_p->order_status == 'picked_up') selected @endif>{{trans('pickups.status.picked_up')}}
                              </option>
                            </select>
                          </td>
                        </tr>
                      </table>
                    </div>
                  <!-- /.info-box -->
                </div>
                @endforeach
              @else
                <p class="no_data">{{trans('common.no_data')}}</p>
              @endif   
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

</script>
@endsection
