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
    {{ trans('catering_orders.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('catering_orders.index')}}">{{ trans('catering_orders.heading') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ trans('catering_orders.w_title') }}
         
          <!-- <h3 class="box-title pull-right"><a href="{{route('reservations.create')}}" class="btn btn-danger pull-right">{{trans('reservations.add_new')}}</a></h3> -->
         
        </div>
        <div class="box-body">
          <table id="reservations" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('catering_orders.customer') }}</th>
                <th>{{ trans('catering_orders.contact') }}</th>
                <th>{{ trans('catering_orders.order_id') }}</th>
                <th>{{ trans('catering_orders.date') }}</th>
                <th>{{ trans('common.status') }}</th>
                <th>{{trans('common.action')}}</th>
              

              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
               <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('catering_orders.customer') }}</th>
                <th>{{ trans('catering_orders.contact') }}</th>
                <th>{{ trans('catering_orders.order_id') }}</th>
                <th>{{ trans('catering_orders.date') }}</th>
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
          url: "{{route('dt_catering_orders')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
          { data: 'id' },
          { data: 'first_name' },
          { data: 'phone_number' },
          { data: 'id' },
          { 
            mRender : function(data, type, row) {
               
                return row["date"]; 
                
              }, 
          },
          { data: 'order_status',
          mRender : function(data, type, row) {
            console.log(data);
                  var status = data;
                  booked_selected = '';
                  cancelled_selected = '';
                  completed_selected = '';

                  if(status=='booked'){
                    booked_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="booked"'+ booked_selected+'>{{trans('catering_orders.status.booked')}}</option><option value="cancelled"'+cancelled_selected+'>{{trans('catering_orders.status.cancelled')}}</option><option value="completed"'+completed_selected+'>{{trans('catering_orders.status.completed')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else if(status=='cancelled'){                  
                     cancelled_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="cancelled"'+cancelled_selected+'>{{trans('catering_orders.status.cancelled')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else if(status=='completed'){ 
                    completed_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="completed"'+completed_selected+'>{{trans('catering_orders.status.completed')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                 
              },
              orderable: false,
              searchable: false
          },
          { 
            mRender : function(data, type, row) {
               
                return '<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>'; 
                
              }, 
              orderable: false,
              searchable: false
          },
        ]
   });
});
</script>

@endsection