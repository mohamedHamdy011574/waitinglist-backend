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
.title_detail_link {margin-right: 15px; color: #dd4b39}

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
span.label-danger {font-size: 14px;}
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
      <span class="badge" id="reservations_badge">{{$badge_count}}</span>
      <a href="{{route('one_view','reservations')}}" class="btn btn-danger">{{ trans('one_view.reservation') }}</a>
    </div>
    @endif

    @if($waiting_list_button > 0)
    <div class="col-md-2">
      <!-- <span class="badge bg-default">3</span> -->
      <a href="{{route('one_view','waiting_list')}}" class="btn btn-default">{{ trans('one_view.waiting_list') }}</a>
    </div>
    @endif

    @if($pickup_button > 0)
    <div class="col-md-2">
      <!-- <span class="badge bg-orange">3</span> -->
      <a href="{{route('one_view','pickups')}}" class="btn btn-default">{{ trans('one_view.pick_up') }}</a>
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
            <a class="title_detail_link" href="{{ route('one_view','reservations') }}">
              <i class="fa fa-arrow-left"></i>
            </a>
            {{ trans('one_view.reservation_id') }}: {{$reservation->id}}
          </h3>
          <div class="col-md-4 col-sm-6 col-xs-12">
            
            <div class="info-box">
              <!-- Format Date -->
                @php 
                $date = \Carbon\Carbon::parse($reservation->check_in_date);
                $reservation->check_in_date = $date->format('dS M Y h:i A');
                

                $classname = '';
                if($reservation->status == 'confirmed'){
                  $classname  = 'confirmed_status';
                }
                if($reservation->status == 'cancelled'){
                  $classname  = 'cancelled_status';
                }
                if($reservation->status == 'checked_in'){
                  $classname  = 'checked_in_status';
                }
                if($reservation->status == 'checked_out'){
                  $classname  = 'checked_out_status';
                }



                @endphp
                 
                <table class="table_info">
                        
                        <tr>
                          <td>
                            <b>{{ trans('one_view.date') }}:</b> 
                          </td>
                          <td>{{$reservation->check_in_date}}</td>
                        </tr>
                        <tr>
                          <td>
                            <b>{{ trans('one_view.name') }}:</b>
                          </td>
                          <td>{{$reservation->first_name}}</td>
                        </tr>
                        <tr>
                          <td>
                            <b>{{ trans('one_view.phone_number') }}:</b>
                          </td>
                          <td>{{$reservation->phone_number}}</td>
                        </tr>
                        <tr>
                          <td>
                            <b>{{ trans('one_view.persons_booked') }}:</b>
                          </td>
                          <td><span class="label label-danger">{{$reservation->reserved_chairs}}</span> </td>
                        </tr>
                </table>
                
                    
                <table class="table_btns">
                  <tr>
                    <td class="cancel_btn">
                      @if($reservation->status != 'cancelled')
                      <a href ="javascript:void(0)" @if($reservation->status == 'cancelled' || $reservation->status != 'confirmed') disabled @endif onclick="return cancel_reservation('{{$reservation->id}}')" class="btn btn-danger">{{ trans('one_view.cancel') }}</a>
                      @endif
                    </td>
                    <td class="btn_space">&nbsp;</td>
                    <td class="status_btn">
                      <select class="btn btn-info status_drpd {{$classname}}" id="{{$reservation->id}}" @if($reservation->status == 'cancelled') disabled @endif>
                        @if($reservation->status == 'confirmed' || ($reservation->status != 'checked_in' && $reservation->status != 'checked_out'))
                        <option value="confirmed" 
                          @if($reservation->status == 'confirmed') selected @endif>{{trans('reservations.status.confirmed')}}
                        </option>
                        @endif
                        @if($reservation->status == 'cancelled')
                          <option value="cancelled"
                            @if($reservation->status == 'cancelled') selected @endif>{{trans('reservations.status.cancelled')}}
                          </option>
                        @endif
                        @if($reservation->status == 'checked_in' || $reservation->status == 'confirmed')
                        <option value="checked_in"
                          @if($reservation->status == 'checked_in') selected @endif>{{trans('reservations.status.checked_in')}}
                        </option>
                        @endif
                        @if($reservation->status == 'checked_out' || $reservation->status == 'checked_in')
                        <option value="checked_out"
                          @if($reservation->status == 'checked_out') selected @endif>{{trans('reservations.status.checked_out')}}
                        </option>
                        @endif
                      </select>
                    </td>
                  </tr>
                </table>
            </div>
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
          url: "{{route('reservation_status')}}",
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
        url: "{{route('reservation_status')}}",
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
