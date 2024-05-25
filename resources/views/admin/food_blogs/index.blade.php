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
   {{trans('food_blogs.heading',['blogger' => $blogger->blogger_name])}} 
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="">{{trans('food_blogs.title')}}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{trans('food_blogs.title')}}</h3>
          <ul class="pull-right">
            <a href="{{route('blogger')}}" class="btn btn-danger">
                <i class="fa fa-arrow-left"></i>
                {{ trans('common.back') }}
            </a>
          </ul>
        </div>
        <div class="box-body">
          <table id="foodblog" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{trans('common.id')}}</th>
                <th>{{trans('food_blogs.cuisines')}}</th>
                <th>{{trans('food_blogs.recipe_name')}}</th>
                <th>{{trans('food_blogs.date')}}</th>
                <th>{{trans('common.status')}}</th>
                <th>{{trans('common.action')}}</th>
              </tr>
            </thead>
           
            <tfoot>
              <tr>
                <th>{{trans('common.id')}}</th>
                 <th>{{trans('food_blogs.cuisines')}}</th>
                 <th>{{trans('food_blogs.recipe_name')}}</th>
                 <th>{{trans('food_blogs.date')}}</th>
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

<script type="text/javascript">
 $(document).ready(function(){
   $('#foodblog').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('dt_food_blog')}}",
          data: {id: "{{$id}}" ,
            "_token": "{{csrf_token()}}"

        },
      },
      columns: [
         { data: 'id' },
         { data: 'cuisine_id',  orderable:false},
         { data: 'recipe_name', orderable:false},
         { data: 'date' ,orderable:false},
       
         // { data: 'description' },
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
                 return '<select class="food_blog_status form-control" id="'+row["id"]+'"><option value="active"'+type+'>'+'{{trans("common.active")}}'+'</option><option value="inactive"'+data+'>'+'{{trans("common.inactive")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
              } ,orderable:false
          },
          { 
            mRender : function(data, type, row) {
                  return '<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>';
              } , orderable:false,
          },
        ]
   });
});
</script>
<script>
  $(document).on('change','.food_blog_status',function(){
      var status = $(this).val();
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('food_blog_status')}}",
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
  
  function delete_alert() {
      if(confirm("{{trans('common.confirm_delete')}}")){
        return true;
      }else{
        return false;
      }
    }
</script>

@endsection