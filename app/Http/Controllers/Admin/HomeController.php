<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use App\Models\WaitingList;
use App\Models\PickupOrder;
use App\Models\PickupOrderItem;
use App\Models\CateringOrder;
use App\Models\Catering;
use App\Models\User;
use App\Models\FoodBlog;
use App\Models\Business;
use App\Models\BusinessBranch;
use App\Models\CateringPackage;
use App\Models\News;
use DB;
use Auth;
use DateTime;
use App\Models\Helpers\CommonHelpers;

class HomeController extends Controller
{
    use CommonHelpers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'vendor_subscribed']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user = auth()->user();
        

          if($user->user_type == 'Vendor') {
            $business = Business::where('vendor_id', Auth::user()->id)->first();
            if(!$business){
              return redirect()->route('businesses.create');
            }
            $total_business_branches = BusinessBranch::where('business_id', $user->business->id)->count();
            $total_staff = User::where(['user_type' => 'WaiterManager', 'vendor_id' => Auth::user()->id])->count();
            $total_catering_package = CateringPackage::where('business_id', $user->business->id)->count();
            return view('admin.home', compact('total_business_branches', 'total_staff', 'total_catering_package','user'));
          }

          if($user->user_type == 'WaiterManager') {
            $staff = $user->business_branch_staff;
            $reservations = -1;
            $waiting_list = -1;
            $pickups = -1;
            $catering_bookings = -1;
            if($staff){
              if($staff->manage_reservations == 1){
                $reservations = Reservation::where('business_branch_id', $staff->business_branch_id)->whereIn('status', ['confirmed','checked_in','checked_out'])->count();
              }
              if($staff->manage_waiting_list == 1){
                $waiting_list = WaitingList::where('business_branch_id', $staff->business_branch_id)->whereIn('status', ['in_queue','checked_in','checked_out'])->count();
              }
              if($staff->manage_pickups == 1){
                $pickups = PickupOrder::where('business_branch_id', $staff->business_branch_id)->whereIn('order_status', ['received','confirmed','ready_for_pickup','picked_up'])->count();
              }
              if($staff->manage_catering_bookings == 1){
                $catering_bookings = CateringOrder::where('business_branch_id', $staff->business_branch_id)->whereIn('order_status', ['booked','completed'])->count();
              }
            }
            
            // echo '<pre>'; print_r($staff); die;
            return view('admin.home', compact('user','reservations','waiting_list', 'pickups', 'catering_bookings'));
          }

          if($user->user_type == 'SuperAdmin') {
  
            $total_customers = User::where(['user_type' => 'Customer'])
                                    ->get()
                                    ->count(); 
            $total_recipe_blogs = FoodBlog::all()->count();
               
            $daily_recipe_blogs = FoodBlog::whereDate('created_at','=',date('Y-m-d'))
                                          ->count();

            $vendors = User::where(['user_type' => 'Vendor', 'status' => 'active'])->count();
            $news = News::where(['status' => 'active'])->count();
            

            $monday = strtotime("last monday");
            $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
            $sunday = strtotime(date("Y-m-d",$monday)." +6 days");
            $this_week_from = date("Y-m-d",$monday);
            $this_week_to = date("Y-m-d",$sunday);

            $this_month_from = date('Y-m-01');
            $this_month_to  = date('Y-m-t');

            // today, this week, this month reservations count         
            $reservation_todays_count = Reservation::whereDate('created_at',date('Y-m-d'))->get()->count();
            $reservation_this_week_count = Reservation::whereBetween('created_at', [$this_week_from, $this_week_to])->get()->count();
            $reservation_this_month_count = Reservation::whereBetween('created_at', [$this_month_from, $this_month_to])->get()->count();   

            // today, this week, this month pickup orders count   
            $pkp_orders_todays_count = PickupOrder::whereDate('created_at',date('Y-m-d'))->get()->count();
            $pkp_orders_this_week_count = PickupOrder::whereBetween('created_at', [$this_week_from, $this_week_to])->get()->count();
            $pkp_orders_this_month_count = PickupOrder::whereBetween('created_at', [$this_month_from, $this_month_to])->get()->count(); 

            // today, this week, this month catering orders count   
            $catering_orders_todays_count = CateringOrder::whereDate('created_at',date('Y-m-d'))->get()->count();
            $catering_orders_this_week_count = CateringOrder::whereBetween('created_at', [$this_week_from, $this_week_to])->get()->count();
            $catering_orders_this_month_count = CateringOrder::whereBetween('created_at', [$this_month_from, $this_month_to])->get()->count();

            // today, this week, this month waiting list orders count   
            $wl_orders_todays_count = WaitingList::whereDate('created_at',date('Y-m-d'))->get()->count();
            $wl_orders_this_week_count = WaitingList::whereBetween('created_at', [$this_week_from, $this_week_to])->get()->count();
            $wl_orders_this_month_count = WaitingList::whereBetween('created_at', [$this_month_from, $this_month_to])->get()->count();

            //Best Selling Menu Items
            $best_selling_items = PickupOrderItem::with('menu')
                                      ->select('menu_id', DB::raw('count(*) as menu_sell_count'))
                                      ->whereHas('pickup_cart',function($q){
                                        $q->where('order_status','!=','cancelled');
                                      })
                                      ->groupBy('menu_id')
                                      ->orderBy('menu_sell_count','desc')
                                      ->take(10)
                                      ->get();

            //Regional distributors
            $regional_distributors = BusinessBranch::with('state')->select('state_id', DB::raw('count(*) as business_count'))
              ->groupBy('state_id')
              ->orderBy('business_count','desc')
              ->get();
            // echo "<pre>";print_r($regional_distributors->toArray());exit;
            //Birth of the users by Years
            $users_birth_by_years = User::where('user_type','Customer')
                                  ->where(DB::raw('YEAR(birth_date)'),'!=','1970')
                                  ->select(DB::raw('YEAR(birth_date) year'), DB::raw('count(id) as `customer_count`'))
                                  ->groupby('year')
                                  ->orderBy('customer_count','desc')
                                  ->get();

            // Peak times in days and hours
            $peak_hours = Reservation::where('status','!=','cancelled')
                                  ->select(DB::raw('WEEKDAY(due_date) week_day'),DB::raw('count(due_time) as `res_count`'),'due_time')
                                  ->groupby('due_time','week_day')
                                  ->orderBy('res_count','desc')
                                  ->get()->toArray();
            $peak_day_hours = [];
            foreach ($peak_hours as $ph_arr) {
              if(!array_key_exists($ph_arr['week_day'], $peak_day_hours))
              {
                $peak_day_hours[$ph_arr['week_day']] = $ph_arr;
              }
            }
            ksort($peak_day_hours);

            // echo "<pre>";print_r($peak_day_hours);exit;
            //Reservation Loyal Customers
            $res_loyal_customers = Reservation::with('customer')
              ->where('status','!=','cancelled')
              ->select('customer_id', DB::raw('count(*) as booking_count'))
              ->groupBy('customer_id')
              ->orderBy('booking_count','desc')
              ->take(20)->get();

            //Pickup Loyal Customers
            $pkp_loyal_customers = PickupOrder::with('customer')
              ->where('order_status','!=','cancelled')
              ->select('customer_id', DB::raw('count(*) as booking_count'))
              ->groupBy('customer_id')
              ->orderBy('booking_count','desc')
              ->take(20)->get();

            //Catering Loyal Customers
            $catering_loyal_customers = CateringOrder::with('customer')
              ->where('order_status','!=','cancelled')
              ->select('customer_id', DB::raw('count(*) as booking_count'))
              ->groupBy('customer_id')
              ->orderBy('booking_count','desc')
              ->take(20)->get();

            //Waiting List Loyal Customers
            $wl_loyal_customers = WaitingList::with('customer')
              ->where('status','!=','cancelled')
              ->select('customer_id', DB::raw('count(*) as booking_count'))
              ->groupBy('customer_id')
              ->orderBy('booking_count','desc')
              ->take(20)->get();
            // echo $total_reservations; die;                              
            return view('admin.home', compact('total_customers', 'total_recipe_blogs', 'daily_recipe_blogs', 'user', 'vendors', 'news','reservation_todays_count','reservation_this_week_count','reservation_this_month_count','pkp_orders_todays_count','pkp_orders_this_week_count','pkp_orders_this_month_count','catering_orders_todays_count','catering_orders_this_week_count','catering_orders_this_month_count','wl_orders_todays_count','wl_orders_this_week_count','wl_orders_this_month_count','best_selling_items','regional_distributors','users_birth_by_years','peak_day_hours','res_loyal_customers','pkp_loyal_customers','catering_loyal_customers','wl_loyal_customers'));
        }

        return redirect()->route('permissions.index');
    }

    //Localization function
    public function lang($locale){
        // echo $locale; die;
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }

    public function get_customer_chart_data(Request $request){
      $year  = date('Y');
      $week  = $request->week_number;
      $dto = new DateTime();
      $dto->setISODate($year, $week);
      $ret['week_start'] = $dto->format('Y-m-d');
      $dto->modify('+6 days');
      $ret['week_end'] = $dto->format('Y-m-d'); 

      $ret_array = [
        'week_start' => date('d M Y', strtotime($ret['week_start'])),
        'week_end' => date('d M Y', strtotime($ret['week_end']))
      ];

      $week_dates = $this->getDatesFromRange($ret['week_start'], $ret['week_end'], 'Y-m-d');
      $mon = $tue = $wed = $thu = $fri = $sat = $sun = 0;
      foreach ($week_dates as $wd) {
        $query = User::whereDate('created_at', $wd)->where(['status' => 'active', 'verified' => 1, 'user_type' => 'Customer']);
        if(date('D', strtotime($wd)) == 'Mon') {
          $mon = $query->count();
          // echo $wd;echo '<br>';echo $mon; die;
        }
        if(date('D', strtotime($wd)) == 'Tue') {
          $tue = $query->count();
        }
        if(date('D', strtotime($wd)) == 'Wed') {
          $wed = $query->count();
        }
        if(date('D', strtotime($wd)) == 'Thu') {
          $thu = $query->count();
        }
        if(date('D', strtotime($wd)) == 'Fri') {
          $fri = $query->count();
        }
        if(date('D', strtotime($wd)) == 'Sat') {
          $sat = $query->count();
        }
        if(date('D', strtotime($wd)) == 'Sun') {
          $sun = $query->count();
        }
      }
      $chart_data = [$mon, $tue, $wed, $thu, $fri, $sat, $sun];
      return response()->json(['week_data' => $ret_array, 'chart_data' => $chart_data]);
    }

    
}
