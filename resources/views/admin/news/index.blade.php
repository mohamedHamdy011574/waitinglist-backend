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
   {{trans('news.heading')}} 
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="{{route('news.index')}}">{{trans('news.title')}}</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{trans('news.title')}}</h3>

          <h3 class="box-title pull-right"><a href="{{route('news.create')}}" class="btn btn-danger pull-right">{{ trans('news.add_new') }}</a></h3> 
      
        </div>
        <div class="box-body">
          <table id="news" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{trans('common.id')}}</th>
                <th>{{trans('news.headline')}}</th>
                <th>{{trans('news.banner')}}</th>
                <th>{{trans('news.description')}}</th>
                <th>{{trans('news.date')}}</th>
                <th>{{trans('news.news_status')}}</th>
                <th>{{trans('common.action')}}</th>
              </tr>
            </thead>
            
            <tfoot>
            <tr>
                <th>{{trans('common.id')}}</th>
                <th>{{trans('news.headline')}}</th>
                <th>{{trans('news.banner')}}</th>
                <th>{{trans('news.description')}}</th>
                <th>{{trans('news.date')}}</th>
                <th>{{trans('news.news_status')}}</th>
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
            url: "{{route('news_status')}}",
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
   $('#news').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('aj_news')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id' },
         { data: 'headline' },
         { data: 'banner',
            mRender : function(data, type, row) {
              return row['banner'];
            }, orderable: false
        },
         { data: 'description' },
         { data: 'date', orderable: false },
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
                 return '<select class="status form-control" id="'+row["id"]+'"><option value="active"'+type+'>Active</option><option value="deactive"'+data+'>Deactive</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
              } 
          },
          { 
            mRender : function(data, type, row) {
                  return '<form action="'+row["edit"]+'" method="get"><button class="btn" type="submit"><i class="fa fa-edit"></i></button></form><form action="'+row["show"]+'" method="get"><button class="btn" type="submit"><i class="fa fa-eye"></i></button>@csrf</form>';
              } ,  orderable: false
          },
        ]
   });
});
</script>
@endsection