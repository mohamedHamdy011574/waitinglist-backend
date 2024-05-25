@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')}}">
<style type="text/css">
  td form{
    display: inline;
  }
  .dt-buttons{margin-right: 10px}
  
</style>
@endsection  
<style>
  #next_week,#prev_week {cursor: pointer;}
  .week_setting_table{width: 100%; text-align: center; margin-top: 10px}
  .week_setting_table p{margin-bottom: 6px}
  #from_date, #to_date {font-size: 16px; font-weight: 900; margin: 0px 5px }
  .fa-chevron-right{color: #dd4b39}
  #chart_loader {font-size: 15px; margin-top: 20px; font-style: italic;}
  .pagination {
    display: inline-flex !important;
  }
</style>
<!-- Info boxes -->
  <div class="row">
    <!-- <div class="col-md-12 col-sm-12 col-xs-12">
      <p class="statistics_title">{{trans('dashboard.total_statistics')}}</p>
    </div> -->
   
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('customers.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="fa fa-users" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.customers')}}">{{trans('dashboard.customers')}}</span>
            <span class="info-box-number">{{$total_customers}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('blogger')}}">
        <div class="info-box">
          <span class="info-box-icon bg-blue">
            <i class="fa fa-birthday-cake" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.recipe_blogs')}}</span>
            <span class="info-box-number">{{$total_recipe_blogs}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('vendors.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-green">
            <i class="fa fa-users" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.vendors')}}</span>
            <span class="info-box-number">{{$vendors}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('news.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-yellow">
            <i class="fa fa-newspaper-o" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.news')}}</span>
            <span class="info-box-number">{{$news}}</span>
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
      <p class="statistics_title">{{trans('dashboard.todays_orders')}}</p>
    </div>
   
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('reservations.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.customers')}}">{{trans('dashboard.reservations')}}</span>
            <span class="info-box-number">{{$reservation_todays_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-blue">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.pickups')}}</span>
            <span class="info-box-number">{{$pkp_orders_todays_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-green">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.catering')}}</span>
            <span class="info-box-number">{{$catering_orders_todays_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-yellow">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.waiting_list')}}</span>
            <span class="info-box-number">{{$wl_orders_todays_count}}</span>
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
      <p class="statistics_title">{{trans('dashboard.this_week_orders')}}</p>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('reservations.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.customers')}}">{{trans('dashboard.reservations')}}</span>
            <span class="info-box-number">{{$reservation_this_week_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-blue">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.pickups')}}</span>
            <span class="info-box-number">{{$pkp_orders_this_week_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-green">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.catering')}}</span>
            <span class="info-box-number">{{$catering_orders_this_week_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-yellow">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.waiting_list')}}</span>
            <span class="info-box-number">{{$wl_orders_this_week_count}}</span>
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
      <p class="statistics_title">{{trans('dashboard.this_month_orders')}}</p>
    </div>
   
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="{{route('reservations.index')}}">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.customers')}}">{{trans('dashboard.reservations')}}</span>
            <span class="info-box-number">{{$reservation_this_month_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-blue">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.pickups')}}</span>
            <span class="info-box-number">{{$pkp_orders_this_month_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-green">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.catering')}}</span>
            <span class="info-box-number">{{$catering_orders_this_month_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <a class="dash_statistics_link" href="#">
        <div class="info-box">
          <span class="info-box-icon bg-yellow">
            <i class="fa fa-braille" aria-hidden="true"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text" title="{{trans('dashboard.recipe_blogs')}}">{{trans('dashboard.waiting_list')}}</span>
            <span class="info-box-number">{{$wl_orders_this_month_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
      </a>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">{{trans('dashboard.best_selling_products')}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>#</th>
                <th>{{trans('dashboard.item')}}</th>
                <th>{{trans('dashboard.price')}}</th>
                <th>{{trans('dashboard.selling_count')}}</th>
              </tr>
              </thead>
              <tbody>
                @foreach($best_selling_items as $key => $item)
                <tr>
                  <td>{{$key+1}}</td>
                  <td>{{$item->menu->name}}</td>
                  <td>{{$item->menu->price}}</td>
                  <td>{{$item->menu_sell_count}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.table-responsive -->
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">{{trans('dashboard.peak_times')}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>#</th>
                <th>{{trans('dashboard.day')}}</th>
                <th>{{trans('dashboard.peak_slot')}}</th>
                <th>{{trans('dashboard.no_of_reservations')}}</th>
              </tr>
              </thead>
              <tbody>
                <?php
                  $weekDays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                ?>
                @foreach($peak_day_hours as $key => $peak_time)
                <tr>
                  <td>{{$key+1}}</td>
                  <td>{{$weekDays[$peak_time['week_day']]}}</td>
                  <td>{{$peak_time['due_time']}}</td>
                  <td>{{$peak_time['res_count']}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.table-responsive -->
        </div>
      </div>
    </div>
    
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">{{trans('dashboard.users_birth_by_years')}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin stastics_table" id="birth_years">
              <thead>
              <tr>
                <th>#</th>
                <th>{{trans('dashboard.birth_year')}}</th>
                <th>{{trans('dashboard.no_of_customers')}}</th>
              </tr>
              </thead>
              <tbody>
                @foreach($users_birth_by_years as $key => $data)
                <tr>
                  <td>{{$key+1}}</td>
                  <td>{{$data->year}}</td>
                  <td>{{$data->customer_count}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.table-responsive -->
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">{{trans('dashboard.regional_distributors')}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin stastics_table" id="reg_dist">
              <thead>
              <tr>
                <th>#</th>
                <th>{{trans('dashboard.state')}}</th>
                <th>{{trans('dashboard.no_of_business')}}</th>
              </tr>
              </thead>
              <tbody>
                @foreach($regional_distributors as $key => $dist)
                <tr>
                  <td>{{$key+1}}</td>
                  <td>{{$dist->state->name}}</td>
                  <td>{{$dist->business_count}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.table-responsive -->
        </div>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">{{trans('dashboard.loyal_customers')}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#reservations" data-toggle="tab">{{trans('dashboard.reservations')}}</a></li>
            <li><a href="#pickups" data-toggle="tab">{{trans('dashboard.pickups')}}</a></li>
            <li><a href="#catering" data-toggle="tab">{{trans('dashboard.catering')}}</a></li>
            <li><a href="#waiting_list" data-toggle="tab">{{trans('dashboard.waiting_list')}}</a></li>
            
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="reservations">
              <div class="table-responsive">
                <table class="table no-margin stastics_table">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>{{trans('dashboard.customer_name')}}</th>
                    <th>{{trans('dashboard.customer_email')}}</th>
                    <th>{{trans('dashboard.customer_contact')}}</th>
                    <th>{{trans('dashboard.no_of_reservations')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($res_loyal_customers as $key => $res)
                    <tr>
                      <td>{{$key+1}}</td>
                      <td>{{$res->customer->first_name.' '.$res->customer->last_name}}</td>
                      <td>{{$res->customer->email}}</td>
                      <td>{{$res->customer->phone_number}}</td>
                      <td>{{$res->booking_count}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="pickups">
              <div class="table-responsive">
                <table class="table no-margin stastics_table">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>{{trans('dashboard.customer_name')}}</th>
                    <th>{{trans('dashboard.customer_email')}}</th>
                    <th>{{trans('dashboard.customer_contact')}}</th>
                    <th>{{trans('dashboard.no_of_pickup')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($pkp_loyal_customers as $key => $res)
                    <tr>
                      <td>{{$key+1}}</td>
                      <td>{{$res->customer->first_name.' '.$res->customer->last_name}}</td>
                      <td>{{$res->customer->email}}</td>
                      <td>{{$res->customer->phone_number}}</td>
                      <td>{{$res->booking_count}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="catering">
              <div class="table-responsive">
                <table class="table no-margin stastics_table">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>{{trans('dashboard.customer_name')}}</th>
                    <th>{{trans('dashboard.customer_email')}}</th>
                    <th>{{trans('dashboard.customer_contact')}}</th>
                    <th>{{trans('dashboard.no_of_catering')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($catering_loyal_customers as $key => $res)
                    <tr>
                      <td>{{$key+1}}</td>
                      <td>{{$res->customer->first_name.' '.$res->customer->last_name}}</td>
                      <td>{{$res->customer->email}}</td>
                      <td>{{$res->customer->phone_number}}</td>
                      <td>{{$res->booking_count}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="waiting_list">
              <div class="table-responsive">
                <table class="table no-margin stastics_table">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>{{trans('dashboard.customer_name')}}</th>
                    <th>{{trans('dashboard.customer_email')}}</th>
                    <th>{{trans('dashboard.customer_contact')}}</th>
                    <th>{{trans('dashboard.no_of_waiting_list')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($wl_loyal_customers as $key => $res)
                    <tr>
                      <td>{{$key+1}}</td>
                      <td>{{$res->customer->first_name.' '.$res->customer->last_name}}</td>
                      <td>{{$res->customer->email}}</td>
                      <td>{{$res->customer->phone_number}}</td>
                      <td>{{$res->booking_count}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- /.tab-content -->
        </div>
        </div>
      </div>
      <!-- Custom Tabs -->
      
      <!-- nav-tabs-custom -->
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <?php
        function getStartAndEndDate($week, $year) 
        {
          $dto = new DateTime();
          $dto->setISODate($year, $week);
          $ret['week_start'] = $dto->format('Y-m-d');
          $dto->modify('+6 days');
          $ret['week_end'] = $dto->format('Y-m-d');
          return $ret;
        }

        $ddate = date('Y-m-d');
        $date = new DateTime($ddate);
        $week_number = $date->format("W");
        $current_week = $date->format("W");
        //echo "Weeknummer: $week_number";
        $week_array = getStartAndEndDate($week_number,2020);
        //echo '<pre>'; print_r($week_array);
      ?>
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{trans('dashboard.customers_vs_time')}}
          </h3>
        </div>
        <div class="box-body chart-responsive">
          <input type="hidden" id="week_number" value="{{$week_number}}">
          <table class="week_setting_table">
            <tr>
              <td style="width: 10%"><p id="prev_week"><i class="fa fa-arrow-left"></i></p></td>
              <td><p id="current_week">{{trans('dashboard.current_week')}}</p>
                <span id="from_date">{{date('d M Y', strtotime($week_array['week_start']))}}</span> <i class="fa fa-chevron-right"></i>
                <span id="to_date">{{date('d M Y', strtotime($week_array['week_end']))}}</span></td>
              <td style="width: 10%"><p id="next_week"><i class="fa fa-arrow-right"></i></p></td>
            </tr>
          </table>
          <canvas id="myChart" width="400" height="400"></canvas>
          <p id="chart_loader" class="text-center" style="display: none">...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- fix for small devices only -->
  <div class="clearfix visible-sm-block"><hr></div>


@section('js')

<script src="{{asset('admin/bower_components/datatables.net-bs/export/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('admin/bower_components/datatables.net-bs/export/jszip.min.js')}}"></script>
<script src="{{asset('admin/bower_components/datatables.net-bs/export/buttons.html5.min.js')}}"></script>
<script src="{{asset('admin/bower_components/datatables.net-bs/export/buttons.print.min.js')}}"></script>

@include('admin.dashboard.adminjs')
<script> 
$(document).ready(function () {
  set_customers_chart('{{$week_number}}');
  $('#next_week').click(function(){
      var week_number = parseInt($('#week_number').val())+1;
      set_customers_chart(week_number);
  });
  $('#prev_week').click(function(){
      var week_number = parseInt($('#week_number').val())-1;
      set_customers_chart(week_number);
  });

  $('.stastics_table').DataTable({
    pageLength : 5, 
    lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']],
    responsive: true
  });
});

function set_customers_chart(week_number) {
    $('#week_number').val(week_number);
    $.ajax({
        type:'post',
        url: "{{route('get_customer_chart_data')}}",
        data: {
                "week_number": week_number,   
                "_token": "{{ csrf_token() }}"
        },
        beforeSend: function () {
          $('#myChart').hide();
          $('#chart_loader').show();
          $('#chart_loader').html('...');
        },
        success: function (data) {
          $('#myChart').show();
          $('#chart_loader').hide();
          $('#from_date').html(data.week_data.week_start);
          $('#to_date').html(data.week_data.week_end);
          myChart.data.datasets = [{
            label: "{{trans('dashboard.customers')}}",
            data: data.chart_data,
            backgroundColor: '#dd4b39',
          }];

          var total = 0;
          for (var i = 0; i < data.chart_data.length; i++) {
              total += data.chart_data[i] << 0;
          }
          if(total == 0) {
            $('#myChart').hide();
            $('#chart_loader').show();
            $('#chart_loader').html("{{trans('dashboard.chart_no_data')}}");
          }

          if({{$current_week}} <= week_number){
            $('#next_week').css('color', '#bbbbbb');
            $('#next_week').css('pointer-events', 'none');
          } else {
            $('#next_week').css('color', 'black');
            $('#next_week').css('pointer-events', 'all');
          }
          // console.log(data);
          myChart.update();
        },
        error: function () {
          toastr.error(data.error);
        }
    })
  }
</script>
@endsection