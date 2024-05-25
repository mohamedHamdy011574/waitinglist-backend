@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')}}">
<style type="text/css">
  td form{
    display: inline;
  }
  .dt-buttons{margin-right: 10px}
</style>
@endsection    
@section('content')
  <section class="content-header">
    <h1>
      {{trans('subscription_packages.plural')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
      <li><a href="{{route('subscription_packages.index')}}">{{trans('subscription_packages.plural')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{ trans('subscription_packages.title') }}</h3>
            @can('subscription-package-create')
            <h3 class="box-title pull-right"><a href="{{route('subscription_packages.create')}}" class="btn btn-danger pull-right">{{trans('subscription_packages.add_new')}}</a></h3>
            @endcan
          </div>
          <div class="box-body">
            <table id="subscription_packages_table" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('subscription_packages.package_name')}}</th>
                  <th>{{trans('subscription_packages.branches_include')}}</th>
                  <th>{{trans('subscription_packages.description')}}</th>
                  <th>{{trans('subscription_packages.subscription_period')}}</th>
                  <th>{{trans('subscription_packages.package_price')}}</th>
                  <th>{{trans('subscription_packages.currency')}}</th>
                  <th>{{trans('common.status')}}</th>
                  <th>{{trans('common.action')}}</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('subscription_packages.package_name')}}</th>
                  <th>{{trans('subscription_packages.branches_include')}}</th>
                  <th>{{trans('subscription_packages.description')}}</th>
                  <th>{{trans('subscription_packages.subscription_period')}}</th>
                  <th>{{trans('subscription_packages.package_price')}}</th>
                  <th>{{trans('subscription_packages.currency')}}</th>
                  <th>{{trans('common.status')}}</th>
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

<script src="{{asset('admin/bower_components/datatables.net-bs/export/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('admin/bower_components/datatables.net-bs/export/jszip.min.js')}}"></script>
<script src="{{asset('admin/bower_components/datatables.net-bs/export/buttons.html5.min.js')}}"></script>
<script src="{{asset('admin/bower_components/datatables.net-bs/export/buttons.print.min.js')}}"></script>
<script type="text/javascript">
  $(document).on('change','.subscription_package_status',function(){
      var status = $(this).val();
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('subscription_package_status')}}",
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
            // console.log(data.success);
              if(data.success){
                toastr.success(data.success);
              }else{
                toastr.error(data.error);
              }
          },
          error: function () {
            toastr.error(data.error);
          }
      })
  })
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#subscription_packages_table').DataTable({
      aaSorting: [[ 0, "desc" ]],
      dom: 'Blfrtip',
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      pageLength: 10,
      buttons: [
        {
            extend: 'excel',
            text: '<span class="fa fa-file-excel-o"></span> {{trans("common.export")}}',
            exportOptions: {
                modifier: {
                    search: 'applied',
                    order: 'applied'
                },
                columns: [ 0, 1]
            },
        },
        {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i> {{trans("common.print")}}',
            autoPrint: true,
            exportOptions: {
                modifier: {
                    search: 'applied',
                    order: 'applied'
                },
                columns: [ 0, 1]
            },
        }

    ],
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span> '},
      ajax: {
          url: "{{route('dt_subscription_packages')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id' },
         { data: 'package_name', },
         { data: 'branches_include', },
         { data: 'description', },
         { data: 'subscription_period', },
         { data: 'package_price', },
         { data: 'currency', },
         { data: 'status',
            mRender : function(data, type, row) {
                  var status=data;
                  if(status=='active'){
                    type="selected";
                    data='';
                  }else{
                    data='selected';
                    type='';
                  }
                 return '<select class="subscription_package_status form-control" id="'+row["id"]+'"><option value="active"'+type+'>'+'{{trans("common.active")}}'+'</option><option value="inactive"'+data+'>'+'{{trans("common.inactive")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">'+'{{trans("common.inactive")}}'+'</span></span>';
              } 
          },
         { 
            mRender : function(data, type, row) {
                 return '@can("subscription-package-edit")<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a>@endcan<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>@can("subscription-package-delete")<form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>@endcan';  
              }, orderable: false, searchable: false
          },
        ]
    });
  });
</script>
<script>
  function delete_alert() {
      if(confirm("{{trans('common.confirm_delete')}}")){
        return true;
      }else{
        return false;
      }
    }
</script>
@endsection