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
    {{ trans('staff.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('customers.index')}}">{{ trans('staff.plural') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ trans('staff.title') }}
          <h3 class="box-title pull-right"><a href="{{route('staff.create')}}" class="btn btn-danger pull-right">{{trans('staff.add_new')}}</a></h3>
        </div>
        <div class="box-body">
          <table id="customers" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('staff.first_name') }}</th>
                <th>{{ trans('staff.last_name') }}</th>
                <th>{{ trans('staff.email') }}</th>
                <th>{{ trans('staff.phone_number') }}</th>
               <!--  <th>{{ trans('staff.restaurant_branch_name') }}</th> -->
                <th>{{ trans('common.status') }}</th>
                <th>{{trans('common.action')}}</th>
              

              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
               <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('staff.first_name') }}</th>
                <th>{{ trans('staff.last_name') }}</th>
                <th>{{ trans('staff.email') }}</th>
                <th>{{ trans('staff.phone_number') }}</th>
                <!-- <th>{{ trans('staff.restaurant_branch_name') }}</th> -->
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
            url: "{{route('staff_status')}}",
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
          url: "{{route('dt_staff')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id' },
         { data: 'first_name' },
         { data: 'last_name' },
         { data: 'email' },
         { data: 'phone_number' },
         // { data: 'restaurant_branch_name', orderable:false },
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
                 return '<select class="status" id="'+row["id"]+'"><option value="active"'+active_selected+'>Active</option><option value="inactive"'+inactive_selected+'>Inactive</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only"></span></span>';
              } 
         },
         { 
            mRender : function(data, type, row) {
                 return '<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a><a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>@can("staff-delete")<form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>@endcan';  
              }, orderable: false, searchable: false
          },
        ]
   });
});
</script>

@endsection