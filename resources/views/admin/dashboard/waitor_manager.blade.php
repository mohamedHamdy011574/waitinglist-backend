<!-- Info boxes -->
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <p class="statistics_title">{{trans('dashboard.total_statistics')}}</p>
    </div>
   
  @if($reservations == 0 || $reservations > 0)
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('reservations.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-green">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.staff')}}">{{trans('dashboard.reservations')}}</span>
            <span class="info-box-number">{{$reservations}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  @endif

  @if($waiting_list == 0 || $waiting_list > 0)
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('waiting_list.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.catering_package')}}">{{trans('dashboard.waiting_list')}}</span>
            <span class="info-box-number">{{$waiting_list}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  @endif
  
  @if($pickups == 0 || $pickups > 0)  
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('pickup_orders.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-orange">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.catering_package')}}">{{trans('dashboard.pickups')}}</span>
            <span class="info-box-number">{{$pickups}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  @endif  

  @if($catering_bookings == 0 || $catering_bookings > 0 )  
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('catering_orders.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-blue">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.catering_package')}}">{{trans('dashboard.catering_bookings')}}</span>
            <span class="info-box-number">{{$catering_bookings}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  @endif  
  </div>

