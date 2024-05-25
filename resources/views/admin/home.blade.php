@extends('layouts.admin')

@section('css')
<style type="text/css">
  .statistics_title{font-size: 20px; margin-top: 15px; font-weight: 600}
  a.dash_statistics_link{color: #034f7b !important}
</style>
@endsection
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    {{trans('dashboard.heading')}} 
    <!-- <small>Version 2.0</small> -->
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li class="active">{{trans('dashboard.heading')}}</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  @if($user->user_type == 'Admin' || $user->user_type == 'SuperAdmin')
    @include('admin.dashboard.admin')
  @endif
  
  @if($user->user_type == 'Manager')
    @include('admin.dashboard.manager')
  @endif 

  @if($user->user_type == 'Vendor')
    @include('admin.dashboard.vendor')
  @endif 

  @if($user->user_type == 'WaiterManager')
    @include('admin.dashboard.waitor_manager')
  @endif   
</section>
<!-- /.content -->

@endsection

@section('js')
<!-- jvectormap  -->
<script src="{{ asset('admin/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('admin/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('admin/bower_components/chart.js/Chart.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{ asset('admin/dist/js/pages/dashboard2.js') }}"></script> -->
<!-- AdminLTE for demo purposes -->
<!-- <script src="{{ asset('admin/dist/js/demo.js') }}"></script> -->


@endsection

@section('css')
<!-- <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/_all-skins.min.css') }}"> -->
@endsection
