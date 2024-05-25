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
   {{ trans('states.heading')}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home')}}</a></li>
    <li><a href="{{route('states.index')}}">{{ trans('states.title')}}</a></li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
           <h3 class="box-title">{{ trans('states.title') }}</h3>
          <h3 class="box-title pull-right"><a href="{{route('states.create')}}" class="btn btn-danger pull-right">{{ trans('states.add_new')}}</a></h3>
        </div>
        <div class="box-body">
          <table id="states" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{ trans('common.id')}}</th>
                <th>{{ trans('states.name')}}</th>
                <th>{{ trans('states.country')}}</th>
                <th>{{ trans('common.action')}}</th>
              </tr>
            </thead>
            <tbody>
               
            </tbody>
            <tfoot>
              <tr>
                <th>{{ trans('common.id')}}</th>
                <th>{{ trans('states.name')}}</th>
                <th>{{ trans('states.country')}}</th>
                <th>{{ trans('common.action')}}</th>
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
 $(document).ready(function(){
   $('#states').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('dt_states')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
          { data: 'id' },
          { data: 'name' },
          { data: 'country_id',
            mRender : function(data, type, row) {
                 return row["country_name"];
            } 
          },
          { 
            mRender : function(data, type, row) {
                return '<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a><a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a><form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>';  
              }, orderable: false, searchable: false
          }
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