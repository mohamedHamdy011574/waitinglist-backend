<!-- Info boxes -->
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <p class="statistics_title">{{trans('dashboard.total_statistics')}}</p>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('reservations.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-aqua">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.reservations')}}">{{trans('dashboard.reservations')}}</span>
            <span class="info-box-number">{{$total_reservations}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('reservations.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="fa fa-times" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.cancelled_reservations')}}">{{trans('dashboard.cancelled_reservations')}}</span>
            <span class="info-box-number">{{$total_cancelled_reservations}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  </div>

    <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <p class="statistics_title">{{trans('dashboard.daily_statistics')}}</p>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua">
          <i class="fa fa-braille" aria-hidden="true"></i>
        </span>

        <div class="info-box-content">
          <span class="info-box-text" title="{{trans('dashboard.reservations')}}">{{trans('dashboard.reservations')}}</span>
          <span class="info-box-number">{{$daily_reservations}}</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-red">
          <i class="fa fa-times" aria-hidden="true"></i>
        </span>

        <div class="info-box-content">
          <span class="info-box-text" title="{{trans('dashboard.cancelled_reservations')}}">{{trans('dashboard.cancelled_reservations')}}</span>
          <span class="info-box-number">{{$daily_cancelled_reservations}}</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    
  </div>
    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"><hr></div>
