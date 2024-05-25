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
      {{trans('restaurants.plural')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
      <li><a href="{{route('restaurants.index')}}">{{trans('restaurants.plural')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{ trans('restaurants.title') }}</h3>
            @can('restaurant-create')
            <h3 class="box-title pull-right"><a href="{{route('restaurants.create')}}" class="btn btn-danger pull-right">{{trans('restaurants.add_new')}}</a></h3>
            @endcan
          </div>
          <div class="box-body">
            <!-- Cuisines Filter -->
            <div class="col-xs-4">
              <select class="form-control select2" id="cuisineschooser" name="cuisines[]" multiple data-placeholder="{{trans('restaurants.select_cuisines')}}" required>
                @foreach($cuisines as $cuisine)
                <option value="{{$cuisine->id}}"
                  {{ (collect(old('cuisines'))->contains($cuisine->id)) ? 'selected':'' }}
                  >{{$cuisine->name}}
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-xs-1 no-padding">
              <button class="btn btn-danger pull-left" id="cuisineFiltee">{{trans('common.filter')}}</button>
            </div>
          
            <!-- Cuisines Filter -->
            <table id="restaurants_table" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('restaurants.name')}}</th>
                  <th>{{trans('restaurants.banner')}}</th>
                  <th>{{trans('restaurants.cuisines')}}</th>
                  <th>{{trans('restaurants.registration_date')}}</th>
                  <th>{{trans('common.status')}}</th>
                  <th>{{trans('restaurants.working_status')}}</th>
                  <th>{{trans('common.action')}}</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('restaurants.name')}}</th>
                  <th>{{trans('restaurants.banner')}}</th>
                  <th>{{trans('restaurants.cuisines')}}</th>
                  <th>{{trans('restaurants.registration_date')}}</th>
                  <th>{{trans('common.status')}}</th>
                  <th>{{trans('restaurants.working_status')}}</th>
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
  $(document).on('change','.restaurant_status',function(){
      var status = $(this).val();
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('restaurant_status')}}",
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
  $(document).on('change','.restaurant_working_status',function(){
      var status = $(this).val();
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('restaurant_working_status')}}",
          data: {
                  "working_status": status, 
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
      $('#restaurants_table').DataTable({
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
                  columns: [0, 1, 3, 4]
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
                  columns: [0, 1, 3, 4]
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
            url: "{{route('dt_restaurants')}}",
            data: {"_token": "{{csrf_token()}}", 'cuisines':cuisines},
        },
        columns: [
           { data: 'id' },
           { data: 'name', },
           { data: 'banner', orderable: false,},
           { data: 'cuisines', orderable: false, },
           { data: 'registration_date', },
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
                   return '<select class="restaurant_status form-control" id="'+row["id"]+'"><option value="active"'+type+'>'+'{{trans("common.active")}}'+'</option><option value="inactive"'+data+'>'+'{{trans("common.inactive")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
                } 
            },
            { data: 'working_status',
              mRender : function(data, type, row) {
                    var status=data;
                    var available_selected = ''
                    var busy_selected = ''
                    var closed_selected = ''
                    var order_suspected_selected = ''
                    if(status=='available'){
                      available_selected="selected";
                    }else if(status=='busy'){
                      busy_selected="selected";
                    }
                    else if(status=='closed'){
                      closed_selected="selected";
                    }
                    else if(status=='order_suspected'){
                      order_suspected_selected="selected";
                    }
                   return '<select class="restaurant_working_status form-control" id="'+row["id"]+'"><option value="available"'+available_selected+'>'+'{{trans("restaurants.available")}}'+'</option><option value="busy"'+busy_selected+'>'+'{{trans("restaurants.busy")}}'+'</option><option value="closed"'+closed_selected+'>'+'{{trans("restaurants.closed")}}'+'</option><option value="order_suspected"'+order_suspected_selected+'>'+'{{trans("restaurants.order_suspected")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
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

    //cuisines filter
    $('#cuisineFiltee').click(function() {
      $('#restaurants_table').DataTable().destroy();
      fill_datatable($('#cuisineschooser').val());  
    })
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