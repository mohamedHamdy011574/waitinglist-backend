<?php
$routename = Route::currentRouteName();


// Checking Subscriptios
$subscription = App\Models\Subscription::where('vendor_id',Auth::user()->id)->first();
$catering_modules = 0;
$restaurant_modules = 0;
$food_truck_modules = 0;
if($subscription) {
  if($subscription->for_catering == 1) {
    $catering_modules = 1;
  }
  if($subscription->for_restaurant == 1) {
    $restaurant_modules = 1;
  }
  if($subscription->for_food_truck == 1) {
    $food_truck_modules = 1;
  }
}

$user = Auth::user();
if($user->user_type == 'WaiterManager'){
  $staff_branch = App\Models\BusinessBranchStaff::where(['staff_id' => $user->id])->first();
}

?>
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
     <!--  <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset('admin/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Auth::user()->name }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div> -->
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">{{trans('admin.main_navigation')}}</li>
        <!-- <li class="active treeview menu-open"> -->
        <li class="@if($routename == 'home') active @endif">
          <a href="{{route('home')}}">
            <i class="fa fa-dashboard"></i> <span>{{trans('admin.dashboard')}}</span>
            <span class="pull-right-container">
              <!-- <i class="fa fa-angle-left pull-right"></i> -->
            </span>
          </a>
        </li>
        @can('user-list')
        <li class="{{ (request()->is('admin/customers*')) ? 'active' : '' }}">
          <a href="{{route('customers.index')}}">
            <i class="fa fa-users"></i> <span>{{trans('admin.customers')}}</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li>
        @endcan
        
        @can('staff-list')
        <li class="{{ (request()->is('admin/staff*')) ? 'active' : '' }}">
          <a href="{{route('staff.index')}}">
            <i class="fa fa-user"></i> <span>{{trans('admin.staff')}}</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li>
        @endcan
        @can('admin-list')
        <li class="{{ (request()->is('admin/admins*')) ? 'active' : '' }}">
          <a href="{{route('admins.index')}}">
            <i class="fa fa-user-circle-o"></i> <span>{{trans('admin.admins')}}</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li>
        @endcan
        @can('vendor-list')
        <li class="{{ (request()->is('admin/vendors*')) ? 'active' : '' }}">
          <a href="{{route('vendors.index')}}">
            <i class="fa fa-user-circle-o"></i> <span>{{trans('admin.vendors')}}</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li>
        <hr>
        @endcan
        @can('permission-list')
        <li class="{{ (request()->is('admin/permissions*')) ? 'active' : '' }}">
          <a href="{{ route('permissions.index') }}">
            <i class="fa fa-tags"></i> <span>Manage Permissions</span>
          </a>
        </li>
        @endcan
        @can('role-list')
        <li class="{{ (request()->is('admin/roles*')) ? 'active' : '' }}">
          <a href="{{ route('roles.index') }}">
            <i class="fa fa-briefcase"></i> <span>Manage Roles</span>
          </a>
        </li>
        @endcan        
        
        <!-- country_list -->
        @can('country-list')
        <li class="{{ (request()->is('admin/countries*')) ? 'active' : '' }}">
          <a href="{{route('countries.index')}}">
            <i class="fa fa fa-map"></i> <span>{{trans('admin.countries')}}</span>
          </a>
        </li>
        @endcan  

        @can('state-list')
        <!-- state_list -->
        <li class="{{ (request()->is('admin/states*')) ? 'active' : '' }}">
          <a href="{{route('states.index')}}">
            <i class="fa fa-code-fork"></i> <span>{{trans('admin.states')}}</span>
          </a>
        </li>
        @endcan
        
        @can('city-list')
        <!-- city_list --> 
        <!-- <li class="{{ (request()->is('admin/cities*')) ? 'active' : '' }}">
          <a href="{{route('cities.index')}}">
            <i class="fa fa-crosshairs"></i> <span>{{trans('admin.cities')}}</span>
          </a>
        </li> -->
        @endcan
        @can('coupon-list')
        <li class="{{ (request()->is('admin/coupons*')) ? 'active' : '' }}">
          <a href="{{route('coupons.index')}}">
            <i class="fa fa-ticket"></i> <span>{{trans('admin.coupons')}}</span>
          </a>
        </li> 
        @endcan
        
        @can('cuisine-list')
        <hr>
        <li class="{{ (request()->is('admin/cuisines*')) ? 'active' : '' }}">
          <a href="{{route('cuisines.index')}}">
            <i class="fa fa-bars"></i> <span>{{trans('admin.cuisines')}}</span>
          </a>
        </li> 
        @endcan

        @can('seating-area-list')
        <li class="{{ (request()->is('admin/seating_area*')) ? 'active' : '' }}">
          <a href="{{route('seating_area.index')}}">
            <i class="fa fa-table"></i> <span>{{trans('admin.seating_area')}}</span>
          </a>
        </li>
        @endcan

        @can('restaurant-list')
        <li class="{{ (request()->is('admin/restaurants*')) ? 'active' : '' }}">
          <a href="{{route('restaurants.index')}}">
            <i class="fa fa-cutlery"></i> <span>{{trans('admin.restaurants')}}</span>
          </a>
        </li> 
        @endcan

        @can('restaurant-branch-list')

        <li class="{{ (request()->is('admin/restaurant_branches*')) ? 'active' : '' }}">
          <a href="{{route('restaurant_branches.index')}}">
            <i class="fa fa-cutlery"></i> <span>{{trans('admin.restaurant_branches')}}</span>
          </a>
        </li> 
        @endcan
        

        @can('catering-list')
        <hr>
        <li class="{{ (request()->is('admin/catering*')) ? 'active' : '' }}">
          <a href="{{route('catering.index')}}">
            <i class="fa fa-glass"></i> <span>{{trans('admin.catering')}}</span>
          </a>
        </li>
        @endcan 


        @can('food-truck-list')
        <hr>
        <li class="{{ (request()->is('admin/food_trucks*')) ? 'active' : '' }}">
          <a href="{{route('food_trucks.index')}}">
            <i class="fa fa-truck"></i> <span>{{trans('admin.food_trucks')}}</span>
          </a>
        </li>
        @endcan 

        @can('food-truck-branch-list')
        <li class="{{ (request()->is('admin/food_truck_branches*')) ? 'active' : '' }}">
          <a href="{{route('food_truck_branches.index')}}">
            <i class="fa fa-truck"></i> <span>{{trans('admin.food_truck_branches')}}</span>
          </a>
        </li> 
        @endcan


        @if($user->user_type == 'Vendor' || $user->user_type == 'SuperAdmin')  
          <hr>
          @can('one-view')
          <li class="{{ (request()->is('admin/one_view*')) ? 'active' : '' }}">
            <a href="{{route('one_view','reservations')}}">
              <i class="fa fa-users"></i> <span>{{trans('admin.one_view')}}</span>
              <span class="pull-right-container">
              </span>
            </a>
          </li>
          @endcan
          
          @can('reservation-list')
          <li class="{{ (request()->is('admin/reservations*')) ? 'active' : '' }}">
            <a href="{{route('reservations.index')}}">
              <i class="fa fa-braille"></i> <span>{{trans('admin.reservation')}}</span>
            </a>
          </li> 
          @endcan

          @can('waiting-list')
          <li class="{{ (request()->is('admin/waiting_list*')) ? 'active' : '' }}">
            <a href="{{route('waiting_list.index')}}">
              <i class="fa fa-braille"></i> <span>{{trans('admin.waiting_list')}}</span>
            </a>
          </li>
          @endcan

          @can('pickups-order-list')
          <li class="{{ (request()->is('admin/pickup_orders*')) ? 'active' : '' }}">
            <a href="{{route('pickup_orders.index')}}">
              <i class="fa fa-braille"></i> <span>{{trans('admin.pickups')}}</span>
            </a>
          </li> 
          @endcan

          @can('catering-order-list')
          <li class="{{ (request()->is('admin/catering_orders*')) ? 'active' : '' }}">
            <a href="{{route('catering_orders.index')}}">
              <i class="fa fa-braille"></i> <span>{{trans('admin.catering_orders')}}</span>
            </a>
          </li> 
          <li class="{{ (request()->is('admin/one_view_catering_orders*')) ? 'active' : '' }}">
            <a href="{{route('one_view_catering_orders')}}">
              <i class="fa fa-braille"></i> <span>{{trans('admin.catering_orders_one_view')}}</span>
            </a>
          </li> 
          <hr>
          @endcan
        @endif

        <!-- ######## WaiterManager ##########-->
        @if($user->user_type == 'WaiterManager')  
          @php
          $one_view_route = 'reservations';
          if(@$staff_branch->manage_reservations == 0){
            if(@$staff_branch->manage_waiting_list == 1){
              $one_view_route = 'waiting_list';
            }else if(@$staff_branch->manage_pickups == 1){
              $one_view_route = 'pickups';
            }
          }
          @endphp
          <hr>
          @can('one-view')
            <li class="{{ (request()->is('admin/one_view*')) ? 'active' : '' }}">
              <a href="{{route('one_view', $one_view_route)}}">
                <i class="fa fa-users"></i> <span>{{trans('admin.one_view')}}</span>
                <span class="pull-right-container">
                </span>
              </a>
            </li>
          @endcan
          
          @can('reservation-list')
            @if(@$staff_branch->manage_reservations == 1)
            <li class="{{ (request()->is('admin/reservations*')) ? 'active' : '' }}">
              <a href="{{route('reservations.index')}}">
                <i class="fa fa-braille"></i> <span>{{trans('admin.reservation')}}</span>
              </a>
            </li> 
            @endif
          @endcan

          @can('waiting-list')
            @if(@$staff_branch->manage_waiting_list == 1)
            <li class="{{ (request()->is('admin/waiting_list*')) ? 'active' : '' }}">
              <a href="{{route('waiting_list.index')}}">
                <i class="fa fa-braille"></i> <span>{{trans('admin.waiting_list')}}</span>
              </a>
            </li>
            @endif
          @endcan

          @can('pickups-order-list')
            @if(@$staff_branch->manage_pickups == 1)
            <li class="{{ (request()->is('admin/pickup_orders*')) ? 'active' : '' }}">
              <a href="{{route('pickup_orders.index')}}">
                <i class="fa fa-braille"></i> <span>{{trans('admin.pickups')}}</span>
              </a>
            </li> 
            @endif
          @endcan

          @can('catering-order-list')
            @if(@$staff_branch->manage_catering_bookings == 1)
            <li class="{{ (request()->is('admin/catering_orders*')) ? 'active' : '' }}">
              <a href="{{route('catering_orders.index')}}">
                <i class="fa fa-braille"></i> <span>{{trans('admin.catering_orders')}}</span>
              </a>
            </li> 
            <li class="{{ (request()->is('admin/one_view_catering_orders*')) ? 'active' : '' }}">
              <a href="{{route('one_view_catering_orders')}}">
                <i class="fa fa-braille"></i> <span>{{trans('admin.catering_orders_one_view')}}</span>
              </a>
            </li> 
            <hr>
            @endif
          @endcan
        @endif
        <!-- ######## WaiterManager ##########-->

        @can('business-create')
        <li class="{{ (request()->is('admin/businesses*')) ? 'active' : '' }}">
          <a href="{{route('businesses.create')}}">
            <i class="fa fa-info-circle"></i> <span>{{trans('admin.business')}}</span>
          </a>
        </li> 
        <li class="{{ (request()->is('admin/terms_and_conditions*')) ? 'active' : '' }}">
          <a href="{{route('terms_and_conditions.create')}}">
            <i class="fa fa-info-circle"></i> <span>{{trans('admin.terms_and_conditions')}}</span>
          </a>
        </li> 
        @endcan


        @can('business-create')
        <li class="{{ (request()->is('admin/my_subscription')) ? 'active' : '' }}">
          <a href="{{route('my_subscription')}}">
            <i class="fa fa-suitcase"></i> <span>{{trans('admin.my_subscription')}}</span>
          </a>
        </li> 
        @endcan

        @can('business-branch-list')
        <li class="{{ (request()->is('admin/business_branch*')) ? 'active' : '' }}">
          <a href="{{route('business_branches.index')}}">
            <i class="fa fa-code-fork"></i> <span>{{trans('admin.business_branches')}}</span>
          </a>
        </li> 
        @endcan

        @if($catering_modules)
          @can('catering-plan-list')
          <!-- <li class="{{ (request()->is('admin/catering_plan*')) ? 'active' : '' }}">
            <a href="{{route('catering_plans.index')}}">
              <i class="fa fa-bars"></i> <span>{{trans('admin.catering_plans')}}</span>
            </a>
          </li>  -->
          @endcan

          @can('catering-addon-list')
          <li class="{{ (request()->is('admin/catering_addon*')) ? 'active' : '' }}">
            <a href="{{route('catering_addons.index')}}">
              <i class="fa fa-plus-square-o"></i> <span>{{trans('admin.catering_addons')}}</span>
            </a>
          </li> 
          @endcan

        @can('catering-package-category-list')
          <li class="{{ (request()->is('admin/catering_package_categories*')) ? 'active' : '' }}">
            <a href="{{route('catering_package_categories.index')}}">
              <i class="fa fa-plus-square-o"></i> <span>{{trans('admin.catering_package_categories')}}</span>
            </a>
          </li> 
        @endcan

        @can('catering-package-list')
          <li class="{{ (request()->is('admin/catering_packages*')) ? 'active' : '' }}">
            <a href="{{route('catering_packages.index')}}">
              <i class="fa fa-plus-square-o"></i> <span>{{trans('admin.catering_packages')}}</span>
            </a>
          </li> 
        @endcan

        @endif 
        @if($restaurant_modules == 1 || $food_truck_modules == 1)
          @can('menu-category-list')
          <li class="{{ (request()->is('admin/menu_categories*')) ? 'active' : '' }}">
            <a href="{{route('menu_categories.index')}}">
              <i class="fa fa-sitemap"></i> <span>{{trans('admin.menu_categories')}}</span>
            </a>
          </li> 
          @endcan
        @endif  

        @if($restaurant_modules == 1)
          @can('menu-list')
          <li class="{{ (request()->is('admin/restaurant_menus*')) ? 'active' : '' }}">
            <a href="{{route('restaurant_menus.index')}}">
              <i class="fa fa-bars"></i> <span>{{trans('admin.restaurant_menus')}}</span>
            </a>
          </li> 
        @endif  

        @if($food_truck_modules == 1)
          <li class="{{ (request()->is('admin/food_truck_menus*')) ? 'active' : '' }}">
            <a href="{{route('food_truck_menus.index')}}">
              <i class="fa fa-bars"></i> <span>{{trans('admin.food_truck_menus')}}</span>
            </a>
          </li> 
          @endcan
        @endif  

        
        @if($restaurant_modules == 1)
          @can('reservation-hours-list')
          <li class="{{ (request()->is('admin/reservation_hours*')) ? 'active' : '' }}">
            <a href="{{route('reservation_hours.index')}}">
              <i class="fa fa-clock-o"></i> <span>{{trans('admin.reservation_hours')}}</span>
            </a>
          </li> 
          @endcan

          @can('pickup-hours-list')
          <li class="{{ (request()->is('admin/pickup_hours*')) ? 'active' : '' }}">
            <a href="{{route('pickup_hours.index')}}">
              <i class="fa fa-clock-o"></i> <span>{{trans('admin.pickup_hours')}}</span>
            </a>
          </li> 
          @endcan
        @endif  

        @can('news-list')
        <li class="{{ (request()->is('admin/news*')) ? 'active' : '' }}">
          <a href="{{route('news.index')}}">
            <i class="fa fa-newspaper-o"></i> <span>{{trans('admin.news')}}</span>
          </a>
        </li> 
        @endcan

        @can('advertisement-list')
        <li class="{{ (request()->is('admin/advertisements*')) ? 'active' : '' }}">
          <a href="{{route('advertisements.index')}}">
            <i class="fa fa-adn"></i> <span>{{trans('admin.advertisements')}}</span>
          </a>
        </li> 
        @endcan

        @can('cms-list')
        <li class="{{ (request()->is('admin/cms') || request()->is('admin/cms/create') || request()->is('admin/cms/*')) ? 'active' : '' }}">
          <a href="{{route('cms.index')}}">
            <i class="fa fa-file-text"></i> <span>{{trans('admin.cms')}}</span>
          </a>
        </li> 
        @endcan

        @can('cms-list')
        <li class="{{ (request()->is('admin/how_to') || request()->is('admin/how_to/create') || request()->is('admin/how_to/*')) ? 'active' : '' }}">
          <a href="{{route('how_to.index')}}">
            <i class="fa fa-question-circle-o"></i> <span>{{trans('admin.how_to')}}</span>
          </a>
        </li> 
        @endcan

        @can('blog-list')
        <li class="{{ (request()->is('admin/blogger*')) ? 'active' : '' }}">
          <a href="{{route('blogger')}}">
            <i class="fa fa-birthday-cake"></i> <span>{{trans('admin.food_blogs')}}</span>
          </a>
        </li> 
        @endcan

        @can('subscription-package-list')
        <li class="{{ (request()->is('admin/subscription_package*')) ? 'active' : '' }}">
          <a href="{{route('subscription_packages.index')}}">
            <i class="fa fa-tasks"></i> <span>{{trans('admin.subscription_packages')}}</span>
          </a>
        </li> 
        @endcan        

        @can('concern-list')
        <li class="{{ (request()->is('admin/concern*')) ? 'active' : '' }}">
          <a href="{{route('concerns.index')}}">
            <i class="fa fa-hand-paper-o"></i> <span>{{trans('admin.concerns')}}</span>
          </a>
        </li> 
        @endcan

        @can('review-list')
        <li class="{{ (request()->is('admin/review*')) ? 'active' : '' }}">
          <a href="{{route('reviews.index')}}">
            <i class="fa fa-warning"></i> <span>{{trans('admin.reviews')}}</span>
          </a>
        </li> 
        @endcan

        @can('company_details')
        <hr>
        <!-- <li class="{{ (request()->is('admin/company_detail*')) ? 'active' : '' }}">
          <a href="{{route('company.index')}}">
            <i class="fa fa-building"></i> <span>{{trans('admin.company_detail')}}</span>
          </a>
        </li> -->
        @endcan


        <!-- enquiry -->
        @can('enquiry-list')
        <!-- <li class="{{ (request()->is('admin/enquiry')) ? 'active' : '' }}">
          <a href="{{route('enquiry.index')}}">
            <i class="fa fa-envelope"></i> <span>{{trans('admin.enquiry')}}</span>
            <span class="pull-right-container">
              <!-- <i class="fa fa-angle-left pull-right"></i> -->
            </span>
          </a>
        </li> -->
        @endcan

        
        <!-- @can('cms-list')
        <li class="{{ (request()->is('admin/cms') || request()->is('admin/cms/create') || request()->is('admin/cms/*')) ? 'active' : '' }}">
          <a href="{{route('cms.index')}}">
            <i class="fa fa-file-text"></i> <span>Cms</span>
          </a>
        </li> 
        @endcan -->
        @can('setting-list')
        <li class=" {{ (request()->is('admin/settings*')) ? 'active' : '' }}">
          <a href="{{route('settings.index')}}">
            <i class="fa fa fa-cog"></i> <span>{{trans('admin.settings')}}</span>
          </a>
          <!-- <ul class="treeview-menu" style="{{ (request()->is('admin/settings')) ? 'display: block;' : '' }}">
            <li class="{{ (request()->is('admin/settings')) ? 'active' : '' }}">
              <a href="{{route('settings.index')}}">
                  <i class="fa fa-arrow-right"></i>
                  {{trans('admin.site_settings')}}
              </a>
            </li>
          </ul> -->
        </li>
        @endcan  

        @can('notifications')
        <li class="{{ (request()->is('admin/notifications')) ? 'active' : '' }}">
          <a href="{{route('notifications.index')}}">
            <i class="fa fa-bell"></i> <span>{{trans('admin.notifications')}}</span>
            <span class="pull-right-container">
              <!-- <i class="fa fa-angle-left pull-right"></i> -->
            </span>
          </a>
        </li>
        @endcan



                
      </ul>
    </section>
  </aside>