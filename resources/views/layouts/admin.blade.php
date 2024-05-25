<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('adminlte.name', 'Laravel') }} @if(@$page_title) - {{$page_title}} @endif</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
    <link rel="stylesheet" href="{{ asset('admin/bower_components/bootstrap/dist-rtl/css/bootstrap.min.css') }} ">
  <?php }else{ ?>
    <link rel="stylesheet" href="{{ asset('admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }} ">
  <?php } ?>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('admin/bower_components/font-awesome/css/font-awesome.min.css') }} ">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('admin/bower_components/Ionicons/css/ionicons.min.css') }} ">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('admin/bower_components/select2/dist/css/select2.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/multiselect/multiselect.css') }}">
  <!-- Theme style -->
  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
    <link rel="stylesheet" href="{{ asset('admin/dist-rtl/css/AdminLTE.css') }} ">
  <?php }else{ ?>
    <link rel="stylesheet" href="{{ asset('admin/dist/css/AdminLTE.min.css') }} ">
  <?php } ?>

  <link rel="stylesheet" href="{{asset('admin/plugins/datetimepicker/bootstrap-datetimepicker.min.css')}}">

  <!-- <link rel="stylesheet" href="{{ asset('admin/dist/css/AdminLTE.min.css') }} "> -->
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/iCheck/square/blue.css') }} ">

  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl') { ?>
      <link rel="stylesheet" href="{{ asset('admin/dist-rtl/css/bootstrap-rtl.min.css') }}">
      <link rel="stylesheet" href="{{ asset('admin/dist-rtl/css/profile.css') }}">
      <link rel="stylesheet" href="{{ asset('admin/dist-rtl/css/rtl.css') }}">
  <?php } ?>

  <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/skin-red.min.css') }}">
  <!-- CkEditor -->
  <script src="{{ asset('admin/bower_components/ckeditor/ckeditor.js') }}"></script>

  <!-- Toaster -->
  <link rel="stylesheet" href="{{asset('css/toastr.min.css')}}" />
  <link rel="stylesheet" href="{{asset('admin/custom/developer.css')}}" />
  @yield('css')
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-red sidebar-mini">
  <div class="wrapper">

  @include('layouts.elements.header')  
  @include('layouts.elements.sidebar')  
<div class="content-wrapper">
    @yield('content')
</div>
  @include('layouts.elements.footer')
</div>
<!-- ./wrapper -->
<!-- /.login-box -->

<!-- For Multi Select -->
<script src="{{ asset('js/multiselect/multiselect.min.js') }}"></script>
<script>
    document.multiselect('.multiselect1');
</script>

<!-- jQuery 3 -->
<script src="{{ asset('admin/bower_components/jquery/dist/jquery.min.js') }} "></script>
<!-- Bootstrap 3.3.7 -->

<?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl' ){ ?>
    <script src="{{ asset('admin/bower_components/bootstrap/dist-rtl/js/bootstrap.min.js') }}"></script>
<?php }else{ ?>
    <script src="{{ asset('admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<?php } ?>

<script src="{{ asset('admin/plugins/datetimepicker/moment-with-locales.js') }}"></script>
  <script src="{{ asset('admin/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('admin/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('admin/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl' ){ ?>
    <script src="{{ asset('admin/dist-rtl/js/adminlte.min.js') }}"></script>
<?php }else{ ?>
    <script src="{{ asset('admin/dist/js/adminlte.min.js') }}"></script>
<?php } ?>

<!-- AdminLTE for demo purposes -->
<?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl' ){ ?>
    <script src="{{ asset('admin/dist-rtl/js/demo.js') }}"></script>
<?php }else{ ?>
    <script src="{{ asset('admin/dist/js/demo.js') }}"></script>
<?php } ?>



<!-- DataTables -->
<script src="{{asset('admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script>
  $(document).ready(function () {
    <?php if(config('app.locales')[config('app.locale')]['dir'] != 'rtl' ){ ?>
        $('.sidebar-menu').tree()
    <?php }?>
  })
</script>
<!--Toaster JS-->
<script src="{{asset('js/toastr.min.js')}}"></script>
<script type="text/javascript">
    @if(Session::has('success'))
      toastr.success("{{ Session::get('success') }}");
    @elseif(Session::has('error'))
      toastr.error("{{ Session::get('error') }}");
    @elseif(Session::has('warning'))
      toastr.warning("{{ Session::get('warning') }}");
    @elseif(Session::has('info'))
      toastr.info("{{ Session::get('info') }}");
    @endif
</script>
<script type="text/javascript"> 
     
  $('.alert-danger').delay(5000).fadeOut();
    
</script>

<script type="text/javascript" src="{{ asset('admin/custom/jquery.validate.min.js') }}"></script>


<!-- Select2 -->
<script src="{{ asset('admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="{{asset('admin/custom/developer.js')}}" />
@yield('js')

</body>
</html>
