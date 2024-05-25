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
    {{ trans('customers.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('customers.index')}}">{{ trans('customers.plural') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ trans('customers.title') }}
        </div>
        <div class="box-body">
          <table id="customers" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('customers.first_name') }}</th>
                <th>{{ trans('customers.last_name') }}</th>
                <th>{{ trans('customers.email') }}</th>
                <th>{{ trans('customers.phone_number') }}</th>
                <th>{{ trans('common.status') }}</th>
              

              </tr>
            </thead>
            <tbody>
             
            </tbody>
            <tfoot>
               <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('customers.first_name') }}</th>
                <th>{{ trans('customers.last_name') }}</th>
                <th>{{ trans('customers.email') }}</th>
                <th>{{ trans('customers.phone_number') }}</th>
                <th>{{ trans('common.status') }}</th>
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
            url: "{{route('status')}}",
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
            },
            error: function () {
              toastr.error(data.error);
            }
        })
    })
</script>
<script type="text/javascript">
 $(document).ready(function(){
   $('#customers').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('ajax_customers')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id' },
         { data: 'first_name' },
         { data: 'last_name' },
         { data: 'email' },
         { data: 'phone_number' },
         { data: 'status',
        mRender : function(data, type, row) {
                  var status=data;
                  // console.log(status);
                  active_selected = '';
                  inactive_selected = '';
                  blocked_selected = '';

                  if (status=='active'){
                    active_selected="selected";
                  }
                  else if(status=='inactive'){
                  	inactive_selected="selected";
                  }
                  else {
                    blocked_selected='selected';
                  }
                 return '<select class="status form-control" id="'+row["id"]+'"><option value="active"'+active_selected+'>Active</option><option value="inactive"'+inactive_selected+'>Inactive</option><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
              } 
          },
        ]
   });
});
</script>

@endsection