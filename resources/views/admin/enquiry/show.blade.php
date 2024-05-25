@extends('layouts.admin')
@section('content')
<section class="content-header">
  <h1>
    {{$enquiry->full_name}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
    <li><a href="{{route('enquiry.index')}}">{{trans('enquiry.plural')}}</a></li>
    <li class="active">{{trans('common.show')}}</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      	<div class="nav-tabs-custom">
        	<div class="tab-content active">
          		<div class="tab-pane active" id="settings">
            		<table class="table">
		            	<tbody>
		              		<tr>
		                		<th>{{trans('enquiry.full_name')}}</th>
		                		<td>{{$enquiry->full_name}} </td>
		             		</tr>
		              		<tr>
		              			<th>{{trans('enquiry.phone_no')}}</th>
		                		<td>{{$enquiry->phone_no}} </td>
		              		</tr>
		              		<tr>
		              			<th>{{trans('enquiry.email_id')}}</th>
		                		<td>{{$enquiry->email_id}}</td>
		              		</tr>
		            	    <tr>
		              	   		<th>{{trans('enquiry.subject')}}</th>
		                		<td>{{$enquiry->subject}}</td>
		              		</tr>
		                	<tr>
		              			<th>{{trans('enquiry.message')}}</th>
		                		<td>{{$enquiry->message}}</td>
		              		</tr>
		                 	<tr>
		              			<th>{{trans('enquiry.status')}}</th>
		                		<td>{{$enquiry->status}}</td>
		                	</tr>       
		            	</tbody>
        			</table>
          		</div>
        	</div>
        </div>
    </div>
</section>
@endsection
