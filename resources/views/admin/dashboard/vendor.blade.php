<!-- Info boxes -->
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <p class="statistics_title">{{trans('dashboard.total_statistics')}}</p>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('business_branches.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-aqua">
            <i class="fa fa-code-fork " aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.')}}">{{trans('dashboard.business_branches')}}</span>
            <span class="info-box-number">{{$total_business_branches}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('staff.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-green">
            <i class="fa fa-user " aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.staff')}}">{{trans('dashboard.staff')}}</span>
            <span class="info-box-number">{{$total_staff}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('catering_packages.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="fa fa-cutlery" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.catering_package')}}">{{trans('dashboard.catering_packages')}}</span>
            <span class="info-box-number">{{$total_catering_package}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  </div>

