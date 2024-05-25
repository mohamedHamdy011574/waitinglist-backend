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
          <h3 class="box-title">{{ trans('waiting_list.w_title') }}
          
          @can('waiting-list-create')
          <h3 class="box-title pull-right"><a href="{{route('waiting_list.create')}}" class="btn btn-danger pull-right">{{trans('waiting_list.add_new')}}</a></h3>
          @endcan
          
        </div>
        <div class="box-body">
          <table id="reservations" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('waiting_list.customer') }}</th>
                <th>{{ trans('waiting_list.contact') }}</th>
                <th>{{ trans('waiting_list.reserve') }}</th>
                <th>{{ trans('waiting_list.token') }}</th>
                <th>{{ trans('waiting_list.date') }}</th>
                <th>{{ trans('common.status') }}</th>
                <th>{{trans('common.action')}}</th>
              

              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
               <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('waiting_list.customer') }}</th>
                <th>{{ trans('waiting_list.contact') }}</th>
                <th>{{ trans('waiting_list.reserve') }}</th>
                <th>{{ trans('waiting_list.token') }}</th>
                <th>{{ trans('waiting_list.date') }}</th>
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
          url: "{{route('dt_waiting_list')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
          { data: 'id' },
          { data: 'first_name' },
          { data: 'phone_number' },
          { data: 'reserved_chairs' },
          { data: 'token_number' },
          { 
            mRender : function(data, type, row) {
               
                return row["date"]; 
                
              }, 
          },
          { data: 'status',
          mRender : function(data, type, row) {
                  var status = data;
                  in_queue_selected = '';
                  cancelled_selected = '';
                  checked_in_selected = '';
                  checked_out_selected = '';

                  if(status == 'in_queue'){
                   in_queue_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="in_queue"'+in_queue_selected+'>{{trans('waiting_list.status.in_queue')}}</option><option value="checked_in"'+checked_in_selected+'>{{trans('waiting_list.status.checked_in')}}</option><option value="checked_out"'+checked_out_selected+'>{{trans('waiting_list.status.checked_out')}}</option><option value="cancelled"'+cancelled_selected+'>{{trans('waiting_list.status.cancelled')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else if(status=='cancelled'){
                    cancelled_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="cancelled"'+cancelled_selected+'>{{trans('waiting_list.status.cancelled')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else if(status=='checked_in'){                  
                    checked_in_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="checked_in"'+checked_in_selected+'>{{trans('waiting_list.status.checked_in')}}</option><option value="checked_out"'+checked_out_selected+'>{{trans('waiting_list.status.checked_out')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
                  }
                  else
                  {
                    checked_out_selected = 'selected';
                    return '<select class="status form-control" id="'+row["id"]+'"><option value="checked_out"'+checked_out_selected+'>{{trans('waiting_list.status.checked_out')}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans('common.loading')}}</span></span>';
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