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
                <th>{{ trans('common.status') }}</th>
                <!-- <th>{{trans('common.action')}}</th> -->
              

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
                <th>{{ trans('common.status') }}</th>
                <!-- <th>{{trans('common.action')}}</th> -->
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
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('dt_reservations_today')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
          { data: 'id' },
          { data: 'customer_name' },
          { data: 'restaurant_name' },
          { data: 'restaurant_branch_name' },
          { data: 'reserved_chairs' },
          { data: 'check_in_date' },
          { data: 'status',
          mRender : function(data, type, row) {
                  var status=data;
                  reserved_selected = '';
                  cancelled_selected = '';
                  checked_in_selected = '';
                  checked_out_selected = '';

                  if(status=='reserved'){
                    reserved_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="reserved"'+reserved_selected+'>Reserved</option><option value="checked_in"'+checked_in_selected+'>Checked In</option><option value="checked_out"'+checked_out_selected+'>Checked Out</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
                  }
                  else if(status=='cancelled'){
                    cancelled_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="cancelled"'+cancelled_selected+'>Cancelled</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
                  }
                  else if(status=='checked_in'){                  
                    checked_in_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="checked_in"'+checked_in_selected+'>Checked In</option><option value="checked_out"'+checked_out_selected+'>Checked Out</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
                  }
                  else
                  {
                    checked_out_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="checked_out"'+checked_out_selected+'>Checked Out</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
                  }
                 
              },
              orderable: false,
              searchable: false
          },
          /*{ 
            mRender : function(data, type, row) {
               
                return '@can("reservation-edit")<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a>@endcan @can("reservation-delete")<form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>@endcan'; 
                
              }, 
              orderable: false,
              searchable: false
          },*/
        ]
   });
});
</script>

@endsection