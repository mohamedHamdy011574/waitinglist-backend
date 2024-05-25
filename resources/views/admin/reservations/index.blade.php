@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
  td form{
    display: inline; 
  }
</style>
@endsection 
@section('content')
<section class="content-header">
  <h1>
    {{ trans('reservations.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('reservations.index')}}">{{ trans('reservations.plural') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ trans('reservations.title') }}
          @can('reservation-create')
          <!-- <h3 class="box-title pull-right"><a href="{{route('reservations.create')}}" class="btn btn-danger pull-right">{{trans('reservations.add_new')}}</a></h3> -->
          @endcan
        </div>
        <div class="box-body">
          <table id="reservations" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('reservations.customer') }}</th>
                <th>{{ trans('reservations.restaurants') }}</th>
                <th>{{ trans('reservations.rest_branches') }}</th>
                <th>{{ trans('reservations.no_of_persons') }}</th>
                <th>{{ trans('reservations.check_in_date') }}</th>
                <th>{{ trans('reservations.seating_areas') }}</th>
                <th>{{ trans('common.status') }}</th>
                <th>{{trans('common.action')}}</th>
              

              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
               <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('reservations.customer') }}</th>
                <th>{{ trans('reservations.restaurants') }}</th>
                <th>{{ trans('reservations.rest_branches') }}</th>
                <th>{{ trans('reservations.no_of_persons') }}</th>
                <th>{{ trans('reservations.check_in_date') }}</th>
                <th>{{ trans('reservations.seating_areas') }}</th>
                <th>{{ trans('common.status') }}</th>
                <th>{{trans('common.action')}}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection 

@section('js')


 <script type="text/javascript">
    $(document).on('change','.status',function(){
        var status = $(this).val();
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
              setTimeout(function() {
                    element.next('.loading').css('visibility', 'hidden');
                }, delay);
              toastr.success(data.success);
              // location.reload()
            },
            error: function () {
              toastr.error(data.error);
            }
        })
    })
</script>
<script type="text/javascript">
  function delete_alert() {
    if(confirm("{{trans('common.confirm_delete')}}")){
      return true;
    }else{
      return false;
    }
  }
 $(document).ready(function(){
   $('#reservations').DataTable({
      aaSorting: [[ 0, "desc" ]],
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('dt_reservations')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
          { data: 'id' },
          { data: 'customer_name' },
          { data: 'restaurant_name', orderable:false },
          { data: 'restaurant_branch_name', orderable:false },
          { data: 'reserved_chairs' },
          { data: 'check_in_date' },
          { data: 'seating_areas_data', orderable:false },
          @if($user->user_type == 'SuperAdmin')
          { data: 'status',
          mRender : function(data, type, row) {
                  var status=data;
                  if(status=='confirmed'){
                    return "{{trans('reservations.status.confirmed')}}";
                  } else if(status=='cancelled'){
                    return "{{trans('reservations.status.cancelled')}}";
                  } else if(status=='checked_in'){
                    return "{{trans('reservations.status.checked_in')}}";
                  } else if(status=='checked_out'){
                    return "{{trans('reservations.status.checked_out')}}";
                  } else {
                    return "-";
                  }
                 
              },
              orderable: false,
              searchable: false
          },
          @else
          { data: 'status',
          mRender : function(data, type, row) {
                  var status=data;
                  confirmed_selected = '';
                  cancelled_selected = '';
                  checked_in_selected = '';
                  checked_out_selected = '';

                  if(status=='confirmed'){
                    confirmed_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="confirmed"'+confirmed_selected+'>{{trans('reservations.status.confirmed')}}</option><option value="checked_in"'+checked_in_selected+'>{{trans('reservations.status.checked_in')}}</option><option value="cancelled"'+cancelled_selected+'>{{trans('reservations.status.cancelled')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else if(status=='cancelled'){
                    cancelled_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="cancelled"'+cancelled_selected+'>{{trans('reservations.status.cancelled')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else if(status=='checked_in'){                  
                    checked_in_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="checked_in"'+checked_in_selected+'>{{trans('reservations.status.checked_in')}}</option><option value="checked_out"'+checked_out_selected+'>{{trans('reservations.status.checked_out')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else
                  {
                    checked_out_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="checked_out"'+checked_out_selected+'>{{trans('reservations.status.checked_out')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                 
              },
              orderable: false,
              searchable: false
          },
          @endif
          { 
            mRender : function(data, type, row) {
               
                return '@can("reservation-list")<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>@endcan'; 
                
              }, 
              orderable: false,
              searchable: false
          },
        ]
   });
});
</script>

@endsection