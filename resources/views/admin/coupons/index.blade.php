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
    {{ trans('coupons.heading') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="{{route('coupons.index')}}">{{ trans('coupons.plural') }}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ trans('coupons.title') }}
          <h3 class="box-title pull-right"><a href="{{route('coupons.create')}}" class="btn btn-danger pull-right">{{trans('coupons.add_new')}}</a></h3>
        </div>
        <div class="box-body">
          <table id="coupons" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('coupons.name') }}</th>
                <th>{{ trans('coupons.code') }}</th>
                <th>{{ trans('coupons.discount') }}</th>
                <th>{{ trans('coupons.start_date') }}</th>
                <th>{{ trans('coupons.end_date') }}</th>
                <th>{{ trans('common.status') }}</th>
                <th>{{ trans('common.status') }}</th>
                <th>{{trans('common.action')}}</th>
              

              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
               <tr>
                <th>{{ trans('common.id') }}</th>
                <th>{{ trans('coupons.name') }}</th>
                <th>{{ trans('coupons.code') }}</th>
                <th>{{ trans('coupons.discount') }}</th>
                <th>{{ trans('coupons.start_date') }}</th>
                <th>{{ trans('coupons.end_date') }}</th>
                <th>{{ trans('common.status') }}</th>
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
            url: "{{route('coupon_status')}}",
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
   $('#coupons').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('dt_coupons')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id' },
         { data: 'name' },
         { data: 'code' },
         { data: 'discount' },
         { data: 'start_date' },
         { data: 'end_date' },
         { data: 'active',
          mRender : function(data, type, row) {
                  var status=data;
                  // console.log(status);
                  active_selected = '';
                  inactive_selected = '';

                  if(status==1){
                    active_selected="selected";
                  }
                  else {
                    inactive_selected='selected';
                  }
                 return '<select class="status form-control" id="'+row["id"]+'"><option value="1"'+active_selected+'>Active</option><option value="0"'+inactive_selected+'>Inactive</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
              },
              orderable: false,
              searchable: false
          },
          { data: 'status',
          mRender : function(data, type, row) {
                  var status=data;
                  var cdt = new Date();
                  var sdt = new Date(row['start_date']);
                  var edt = new Date(row['end_date']);
                  // console.log(status);
                  active_selected = '';
                  inactive_selected = '';

                  if(status=='cancelled'){
                    return 'Cancelled';
                  }
                  else if(cdt < sdt){
                    return 'Upcoming';
                  }
                  else if(cdt > edt){                  
                    return 'Expired';
                  }
                  else
                  {
                    return 'Ongoing'
                  }
                 // return '<select class="status form-control" id="'+row["id"]+'"><option value="1"'+active_selected+'>Active</option><option value="0"'+inactive_selected+'>Inactive</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
              },
              orderable: false,
              searchable: false
          },
          { 
            mRender : function(data, type, row) {
                var cdt = new Date();
                var dt = new Date(row['start_date']);
                // console.log(row)
                if(row['active'] == 1 && dt > cdt && row['status'] != 'cancelled')
                {
                    return '@can("coupon-edit")<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a>@endcan<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a><a title="Cancel" class="btn" href="{{url("admin/coupons/status/cancel")}}/'+row['id']+'"><i class="fa fa-window-close"></i></a><form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>'; 
                }
                else
                {
                  return '@can("coupon-edit")<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a>@endcan<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a><form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>'; 
                }
              }, 
              orderable: false,
              searchable: false
          },
        ]
   });
});
</script>

@endsection