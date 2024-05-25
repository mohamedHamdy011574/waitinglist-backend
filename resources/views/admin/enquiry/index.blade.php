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
      {{trans('enquiry.heading')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('enquiry.index')}}"> {{trans('enquiry.plural')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{trans('enquiry.title')}}</h3> 
          </div>
          <div class="box-body">
            <table id="enquiry" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('enquiry.full_name')}}</th>
                  <th>{{trans('enquiry.phone_no')}}</th>
                  <th>{{trans('enquiry.email_id')}}</th>
                  <th>{{trans('enquiry.subject')}}</th>
                  <th>{{trans('enquiry.message')}}</th>
                  <th>{{trans('enquiry.status')}}</th>
                  <th>{{trans('common.action')}}</th> 
                </tr>
              </thead>
              <tbody> 

                @foreach($enquirys as $enquiry)
                  <tr>
                    <td>{{$enquiry->id}}</td>
                    <td>{{$enquiry->full_name}}</td>
                    <td>{{$enquiry->phone_no}}</td>
                    <td>{{$enquiry->subject}}</td>
                    <td>{{$enquiry->message}}</td>
                    <td>{{$enquiry->status}}</td>
                  </tr>
                @endforeach  
              </tbody>
              <tfoot>
               <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('enquiry.full_name')}}</th>
                  <th>{{trans('enquiry.phone_no')}}</th>
                  <th>{{trans('enquiry.email_id')}}</th>
                  <th>{{trans('enquiry.subject')}}</th>
                  <th>{{trans('enquiry.message')}}</th>
                  <th>{{trans('enquiry.status')}}</th>
                  <th>{{trans('common.action')}}</th> 
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
    $(document).on('change','.enquiry_status',function(){
        var status = $(this).val();
        var id = $(this).attr('id');
        var delay = 500;
        var element = $(this);
        $.ajax({
            type:'post',
            url: "{{route('enquiry_status')}}",
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
   $('#enquiry').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      ajax: {
          url: "{{route('ajax_enquiry')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id' },
         { data: 'full_name' },
         { data: 'phone_no' },
         { data: 'email_id' },
         { data: 'subject' },
         { data: 'message'},
         { data: 'status',
            mRender : function(data, type, row) {
                  var status=data;
                  if(status=='open'){
                    type="selected";
                    data='';
                  }else{
                    data='selected';
                    type='';
                  }
                 return '<select class="enquiry_status" id="'+row["id"]+'"><option value="open"'+type+'>Open</option><option value="resolved"'+data+'>Resolved</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
              } 
          },
          { 
            mRender : function(data, type, row) {
                  return '<form action="'+row["show"]+'" method="get"><button class="btn" type="submit"><i class="fa fa-eye"></i></button></form><form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>';
              } 
          },
        ]
   });
});
</script>
@endsection