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
     {{ trans('how_to.singular') }} 
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
      <li><a href="{{route('how_to.index')}}">{{ trans('how_to.singular') }}</a></li>
    </ol>
  </section>


  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{trans('how_to.title')}}</h3>
            @can('cms-create')
            <h3 class="box-title pull-right"><a href="{{route('how_to.create')}}" class="btn btn-danger pull-right">{{ trans('how_to.add_new') }}</a></h3>
            @endcan
          </div>
          <div class="box-body">
            <table id="how_to" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{ trans('common.id') }}</th>
                  <th>{{ trans('how_to.question') }}</th>
                  <th>{{ trans('how_to.answer') }}</th>
                  <th>{{ trans('how_to.display_order') }}</th>
                  <th>{{ trans('common.status') }}</th>
                  <th>{{ trans('common.action') }}</th>
                </tr>
              </thead>
              <tbody> 
              </tbody>
              <tfoot>
                <tr>
                  <th>{{ trans('common.id') }}</th>
                  <th>{{ trans('how_to.question') }}</th>
                  <th>{{ trans('how_to.answer') }}</th>
                  <th>{{ trans('how_to.display_order') }}</th>
                  <th>{{ trans('common.status') }}</th>
                  <th>{{ trans('common.action') }}</th>
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
    $(document).on('change','.how_to_status',function(){
        var status = $(this).val();
        var id = $(this).attr('id');
        var delay = 500;
        var element = $(this);
        $.ajax({
            type:'post',
            url: "{{route('how_to_status')}}",
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
   $('#how_to').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('dt_how_to')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id' },
         { data: 'question', },
         { data: 'answer',   },
         { data: 'display_order' },
         { data: 'status',
            mRender : function(data, type, row) {
                  var status=data;
                  if(status=='1'){
                    type="selected";
                    data='';
                  }else{
                    data='selected';
                    type='';
                  }
                 return '<select class="how_to_status form-control" id="'+row["id"]+'"><option value="1"'+type+'>Active</option><option value="0"'+data+'>Inactive</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
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