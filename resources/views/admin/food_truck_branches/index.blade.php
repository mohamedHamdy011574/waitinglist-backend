@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')}}">
<style type="text/css">
  td form{
    display: inline;
  }
  .dt-buttons{margin-right: 10px}
  .select2-selection {padding: 0px 5px !important}
</style>
@endsection    
@section('content')
  <section class="content-header">
    <h1>
      {{trans('food_truck_branches.plural')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
      <li><a href="{{route('food_truck_branches.index')}}">{{trans('food_truck_branches.plural')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{ trans('food_truck_branches.title') }}</h3>
            @can('food-truck-create')
            <h3 class="box-title pull-right"><a href="{{route('food_truck_branches.create')}}" class="btn btn-danger pull-right">{{trans('food_truck_branches.add_new')}}</a></h3>
            @endcan
          </div>
          <div class="box-body">
            <table id="food_truck_branches_table" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('food_truck_branches.food_truck')}}</th>
                  <th>{{trans('food_truck_branches.name')}}</th>
                  <th>{{trans('food_truck_branches.address')}}</th>
                  <th>{{trans('common.status')}}</th>
                  <th>{{trans('common.action')}}</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('food_truck_branches.food_truck')}}</th>
                  <th>{{trans('food_truck_branches.name')}}</th>
                  <th>{{trans('food_truck_branches.address')}}</th>
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
  $(document).on('change','.food_truck_branch_status',function(){
      var status = $(this).val();
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('food_truck_branch_status')}}",
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
    fill_datatable();
    function fill_datatable(cuisines = '') {
      $('#food_truck_branches_table').DataTable({
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
                  columns: [0, 1, 2, 3]
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
                  columns: [0, 1, 2, 3]
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
            url: "{{route('dt_food_truck_branches')}}",
            data: {"_token": "{{csrf_token()}}", 'cuisines':cuisines},
        },
        columns: [
           { data: 'id' },
           { data: 'food_truck_name' },
           { data: 'name' },
           { data: 'address' },
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
                   return '<select class="food_truck_branch_status form-control" id="'+row["id"]+'"><option value="active"'+type+'>'+'{{trans("common.active")}}'+'</option><option value="inactive"'+data+'>'+'{{trans("common.inactive")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
                } 
            },
           { 
              mRender : function(data, type, row) {
                   return '<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a><a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>';  
                }, orderable: false, searchable: false
            },
          ]
      });
    }

    $('.select2').select2();
    
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