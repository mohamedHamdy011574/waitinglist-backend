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
   {{trans('bloggers.heading')}} 
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="{{route('blogger')}}">{{trans('bloggers.title')}}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{trans('bloggers.title')}}</h3>
        </div>
        <div class="box-body">
          <table id="bloggers" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{trans('common.id')}}</th>
                <th>{{trans('bloggers.blogger_name')}}</th>
                <th>{{trans('bloggers.blogger_photo')}}</th>
                <th>{{trans('bloggers.no_of_blog')}}</th>
                <th>{{trans('common.action')}}</th>
              </tr>
            </thead>
            
            <tfoot>
              <tr>
                <th>{{trans('common.id')}}</th>
                <th>{{trans('bloggers.blogger_name')}}</th>
                <th>{{trans('bloggers.blogger_photo')}}</th>
                <th>{{trans('bloggers.no_of_blog')}}</th>
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
 $(document).ready(function(){
   $('#bloggers').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('dt_bloggers')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
          { data: 'id' },
          { data: 'blogger_name',orderable:false},
          { data: 'blogger_photo',orderable:false },
          // { data: 'total_blogs' },
          { 
            mRender : function(data, type, row) {
                if(row.total_blogs > 0)
                  return '<a class="" href="'+row["show"]+'">'+row.total_blogs+'</a>';
                else
                  return row.total_blogs;
              },orderable:false, 
          },
          { 
            mRender : function(data, type, row) {
                  return '<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>';
              } , orderable:false
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