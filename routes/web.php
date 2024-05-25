<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('/xyz', 'HomeController@xyz');
Route::get('/home', 'Admin\HomeController@index');

Route::prefix('admin')->group( function() {
	Auth::routes(['verify' => true]);
});

// Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin','namespace' => 'Admin' ], function() {
    Route::get('permission/unauthorized','PermissionController@unauthorized')->name('permission_error');
});

Route::group(['prefix' => 'admin','namespace' => 'Admin'], function () {
	Route::get('lang/{locale}', 'HomeController@lang')->name('locale');

	Route::middleware(['auth:web','verified'])->group( function() {
		Route::get('/home', 'HomeController@index')->name('home');
        Route::post('get_customer_chart_data', 'HomeController@get_customer_chart_data')->name('get_customer_chart_data');
		
		Route::resource('roles','RoleController');
		Route::resource('permissions','PermissionController')->except(['show','edit','update']);
        Route::resource('users','UserController');
    
		//setting
		Route::get('settings', 'SettingController@index')->name('settings.index');
		Route::post('settings/update', 'SettingController@update')->name('settings.update');

        //profile
        Route::get('profile', 'ProfileController@index')->name('profile.index');
        Route::post('profile/update', 'ProfileController@update')->name('profile.update');

		//countries
		Route::resource('countries','CountryController');
		Route::post('/countries/ajax', 'CountryController@index_ajax')->name('dt_country');
		Route::post('/countries/status', 'CountryController@status')->name('country_status');

		// states
		Route::get('states', 'StateController@index');
		Route::resource('states', 'StateController');
		Route::post('/states/ajax', 'StateController@index_ajax')->name('dt_states');
		Route::post('/country/state', 'StateController@getStatesByCountry')->name('get_states_by_country');

        //get state
        Route::get('state/{coutry_id}', 'StateController@get_state')->name('state.list');

		// cities
		Route::resource('cities', 'CityController');
		Route::post('/get_states_from_country', 'CityController@get_states_from_country')->name('get_states_from_country');
		Route::post('/cities/ajax', 'CityController@index_ajax')->name('dt_cities');

		//cms
		Route::resource('cms', 'CmsController');
		Route::post('/cms/ajax', 'CmsController@index_ajax')->name('dt_cms');
		Route::post('/cms/status', 'CmsController@status')->name('cms_status');

        //terms_and_conditions
        Route::resource('terms_and_conditions', 'TermsConditionController');
        // Route::patch('terms_and_conditions', 'TermsConditionController@update_terms_and_conditions')->name('update_terms_and_conditions');

        //cms
        Route::resource('how_to', 'HowToController');
        Route::post('/how_to/ajax', 'HowToController@index_ajax')->name('dt_how_to');
        Route::post('/how_to/status', 'HowToController@status')->name('how_to_status');
		
		//enquiry
		Route::resource('/enquiry', 'EnquiryController');
		Route::post('/enquiry/ajax', 'EnquiryController@index_ajax')->name('ajax_enquiry');
		Route::post('/enquiry/status', 'EnquiryController@status')->name('enquiry_status');
		
		
		//customer
		Route::resource('customers','CustomersController');
		Route::post('/customers/ajax', 'CustomersController@index_ajax')->name('ajax_customers');
		Route::post('/customers/status', 'CustomersController@status')->name('status');

		//cuisines
		Route::resource('cuisines', 'CuisineController');
		Route::post('/cuisines/ajax', 'CuisineController@index_ajax')->name('dt_cuisines');
		Route::post('/cuisines/status', 'CuisineController@status')->name('cuisine_status');

		//cuisines
		Route::resource('seating_area', 'SeatingAreaController');
		Route::post('/seating_area/ajax', 'SeatingAreaController@index_ajax')->name('dt_seating_area');
		Route::post('/seating_area/status', 'SeatingAreaController@status')->name('seating_area_status');

		//coupons
		Route::resource('coupons', 'CouponController');
		Route::post('/coupons/ajax', 'CouponController@index_ajax')->name('dt_coupons');
		Route::post('/coupons/status', 'CouponController@status')->name('coupon_status');
		Route::get('/coupons/status/cancel/{id}', 'CouponController@cancelCoupon')->name('cancel_coupon');

        //restaurants
        Route::resource('restaurants', 'RestaurantController');
        Route::post('/restaurants/ajax', 'RestaurantController@index_ajax')->name('dt_restaurants');
        Route::post('/restaurants/status', 'RestaurantController@status')->name('restaurant_status');
        Route::post('/restaurants/working_status', 'RestaurantController@workingStatus')->name('restaurant_working_status');
        Route::post('/restaurants/remove_media', 'RestaurantController@remove_media')->name('remove_restaurant_media');

        //restaurant branches
        Route::resource('restaurant_branches', 'RestaurantBranchController');
        Route::post('/restaurant_branches/ajax', 'RestaurantBranchController@index_ajax')->name('dt_restaurant_branches');
        Route::post('/restaurant_branches/status', 'RestaurantBranchController@status')->name('restaurant_branch_status');

        // Catering
        Route::resource('catering', 'CateringController');
        Route::post('/catering/ajax', 'CateringController@index_ajax')->name('dt_catering');
        Route::post('/catering/status', 'CateringController@status')->name('catering_status');
        Route::post('/catering/remove_media', 'CateringController@remove_media')->name('remove_catering_media');

        //food trucks
        Route::resource('food_trucks', 'FoodTruckController');
        Route::post('/food_trucks/ajax', 'FoodTruckController@index_ajax')->name('dt_food_trucks');
        Route::post('/food_trucks/status', 'FoodTruckController@status')->name('food_truck_status');
        Route::post('/food_trucks/remove_media', 'FoodTruckController@remove_media')->name('remove_food_truck_media');

        //food trucks branches
        Route::resource('food_truck_branches', 'FoodTruckBranchController');
        Route::post('/food_truck_branches/ajax', 'FoodTruckBranchController@index_ajax')->name('dt_food_truck_branches');
        Route::post('/food_truck_branches/status', 'FoodTruckBranchController@status')->name('food_truck_branch_status');
		
		//reservations
		Route::resource('reservations', 'ReservationController');
		Route::post('/reservations/ajax', 'ReservationController@index_ajax')->name('dt_reservations');
		Route::post('/reservations/status', 'ReservationController@status')->name('reservation_status');
		Route::post('/reservations/rest_branches', 'ReservationController@getRestBranchesByRest')->name('rest.rest_branches');
		Route::get('/todays_reservations', 'ReservationController@todaysReservations')->name('reservations.today');
		Route::post('/todays_reservations/ajax', 'ReservationController@index_ajax_today')->name('dt_reservations_today');
		// Route::get('/reservations/status/cancel/{id}', 'ReservationController@cancelCoupon')->name('cancel_coupon');

		//News
		Route::resource('news', 'NewsController');
		Route::post('/news/ajax', 'NewsController@index_ajax')->name('aj_news');
		Route::post('/news/status', 'NewsController@status')->name('news_status');

	    //Company Details
		Route::get('company_detail', 'CompanyDetailController@index')->name('company.index');
		Route::post('company_detail/update', 'CompanyDetailController@update')->name('company.update');


        //Managers
        Route::resource('managers', 'ManagerController');
        Route::post('/managers/ajax', 'ManagerController@index_ajax')->name('dt_managers');
        Route::post('/managers/status', 'ManagerController@status')->name('manager_status');


        //Staff
        Route::resource('staff', 'StaffController');
        Route::post('/staff/ajax', 'StaffController@index_ajax')->name('dt_staff');
        Route::post('/staff/status', 'StaffController@status')->name('staff_status');

    	//Blogger list  
        Route::get('bloggers', 'FoodBlogController@blogger_index')->name('blogger');
        Route::get('/blogger_food_post/{id}', 'FoodBlogController@blogger_post')->name('blogger_post');
        Route::post('/blogger/ajax', 'FoodBlogController@blogger_index_ajax')->name('dt_bloggers');
        //Blog food list  
        Route::resource('food_blogs', 'FoodBlogController');
        Route::post('/food_blogs/ajax', 'FoodBlogController@index_ajax')->name('dt_food_blog');
        // Blog food details
        Route::get('/blogger_food_detail/{id}','FoodBlogController@blogger_food_detail')->name('blogger_food_detail');
        Route::post('/food_blogs/status', 'FoodBlogController@status')->name('food_blog_status');


	    // cms
        Route::resource('cms', 'CmsController');
	    Route::post('/cms/ajax', 'CmsController@index_ajax')->name('dt_cms');
	    Route::post('/cms/status', 'CmsController@status')->name('cms_status');

        // Admins
        Route::resource('admins', 'AdminController');
        Route::post('/admins/ajax', 'AdminController@index_ajax')->name('dt_admins');
        Route::post('/admins/status', 'AdminController@status')->name('admin_status');

        Route::resource('notifications','NotificationController');
        //send notifiction route
        Route::post('/notifications','NotificationController@sendNotificationToUsers')->name('send_notification');
  	


    	//subscription packages
    	Route::resource('subscription_packages', 'SubscriptionPackageController');
    	Route::post('/subscription_packages/ajax', 'SubscriptionPackageController@index_ajax')->name('dt_subscription_packages');
    	Route::post('/subscription_packages/status', 'SubscriptionPackageController@status')->name('subscription_package_status');

    		//Vendors
        Route::resource('vendors', 'VendorController');
        Route::post('/vendors/ajax', 'VendorController@index_ajax')->name('dt_vendors');
        Route::post('/vendors/status', 'VendorController@status')->name('vendor_status');


        //Businesses
        Route::resource('businesses', 'BusinessController');
        Route::post('/businesses/ajax', 'BusinessController@index_ajax')->name('dt_businesses');
        Route::post('/businesses/status', 'BusinessController@status')->name('business_status');
        Route::post('/businesses/remove_media', 'BusinessController@remove_media')->name('remove_business_media');

        //business branches
        Route::resource('business_branches', 'BusinessBranchController');
        Route::post('/business_branches/ajax', 'BusinessBranchController@index_ajax')->name('dt_business_branches');
        Route::post('/business_branches/status', 'BusinessBranchController@status')->name('business_branch_status');

        //catering plans
        Route::resource('catering_plans', 'CateringPlanController');
        Route::post('/catering_plans/ajax', 'CateringPlanController@index_ajax')->name('dt_catering_plans');
        Route::post('/catering_plans/status', 'CateringPlanController@status')->name('catering_plan_status');
        Route::post('/catering_plans/remove_media', 'CateringPlanController@remove_media')->name('remove_catering_plan_media');

        //catering addons
        Route::resource('catering_addons', 'CateringAddonController');
        Route::post('/catering_addons/ajax', 'CateringAddonController@index_ajax')->name('dt_catering_addons');
        Route::post('/catering_addons/status', 'CateringAddonController@status')->name('catering_addon_status');

        //Catering Package Category 
        Route::resource('catering_package_categories', 'CateringPackageCategoryController');
        Route::post('/catering_package_categories/ajax', 'CateringPackageCategoryController@index_ajax')->name('dt_catering_package_categories');
        Route::post('/catering_package_categories/status', 'CateringPackageCategoryController@status')->name('catering_package_categories_status');

         //Catering Package
        Route::resource('catering_packages', 'CateringPackageController');
        Route::post('/catering_packages/ajax', 'CateringPackageController@index_ajax')->name('dt_catering_packages');
        Route::post('/catering_packages/status','CateringPackageController@status')->name('catering_packages_status');
        Route::post('/catering_packages/remove_media', 'CateringPackageController@remove_media')->name('remove_catering_package_media');

        //concerns
        Route::resource('concerns', 'ConcernController');
        Route::post('/concerns/ajax', 'ConcernController@index_ajax')->name('dt_concerns');
        Route::post('/concerns/status', 'ConcernController@status')->name('concern_status');

        //advertisements
        Route::resource('advertisements', 'AdvertisementController');
        Route::post('/advertisements/ajax', 'AdvertisementController@index_ajax')->name('dt_advertisements');
        Route::post('/advertisements/status', 'AdvertisementController@status')->name('advertisement_status');

        //review reports
        Route::resource('reviews', 'ReviewController');
        Route::post('/reviews/ajax', 'ReviewController@index_ajax')->name('dt_reported_reviews');
        Route::post('/reviews/status', 'ReviewController@status')->name('review_status');


        //my_subscription
        Route::get('my_subscription', 'SubscriptionController@my_subscription')->name('my_subscription');
        Route::get('choose_subscription', 'SubscriptionController@choose_subscription')->name('choose_subscription');
        Route::post('save_chosen_subscription', 'SubscriptionController@save_chosen_subscription')->name('save_chosen_subscription');

        //reservation hours
        Route::resource('reservation_hours', 'ReservationHoursController');
        Route::post('/reservation_hours/ajax', 'ReservationHoursController@index_ajax')->name('dt_reservation_hours');
        Route::post('/reservation_hours/status', 'ReservationHoursController@status')->name('reservation_hours_status');

        //menu categories
        Route::resource('menu_categories', 'MenuCategoryController');
        Route::post('/menu_categories/ajax', 'MenuCategoryController@index_ajax')->name('dt_menu_categories');
        Route::post('/menu_categories/status', 'MenuCategoryController@status')->name('menu_category_status');

        //restaurant_menus
        Route::resource('restaurant_menus', 'RestaurantMenuController');
        Route::post('/restaurant_menus/ajax', 'RestaurantMenuController@index_ajax')->name('dt_restaurant_menus');
        Route::post('/restaurant_menus/status', 'RestaurantMenuController@status')->name('restaurant_menu_status');

        //food_truck_menus
        Route::resource('food_truck_menus', 'FoodTruckMenuController');
        Route::post('/food_truck_menus/ajax', 'FoodTruckMenuController@index_ajax')->name('dt_food_truck_menus');
        Route::post('/food_truck_menus/status', 'FoodTruckMenuController@status')->name('food_truck_menu_status');

        //pickup hours
        Route::resource('pickup_hours', 'PickupHoursController');
        Route::post('/pickup_hours/ajax', 'PickupHoursController@index_ajax')->name('dt_pickup_hours');
        Route::post('/pickup_hours/status', 'PickupHoursController@status')->name('pickup_hours_status');

        //One View
        Route::get('one_view/{type}', 'OneViewController@one_view')->name('one_view');
        Route::get('one_view/{type}/{id}', 'OneViewController@one_view_detail')->name('one_view_detail');

        //waiting List
        Route::resource('waiting_list', 'WaitingListController');
        Route::post('/waiting_list/ajax', 'WaitingListController@index_ajax')->name('dt_waiting_list');
        Route::post('/waiting_list/status', 'WaitingListController@status')->name('waiting_list_status');
        Route::post('/waiting_list/send_notification','WaitingListController@send_notification')->name('wl_send_notification');

        //pickup_orders
        Route::resource('pickup_orders', 'PickupController');
        Route::post('/pickup_orders/ajax', 'PickupController@index_ajax')->name('dt_pickup_orders');
        Route::post('/pickup_orders/status', 'PickupController@status')->name('pickup_order_status');


        //catering_orders
        Route::resource('catering_orders', 'CateringOrderController');
        Route::post('/catering_orders/ajax', 'CateringOrderController@index_ajax')->name('dt_catering_orders');
        Route::post('/catering_orders/status', 'CateringOrderController@status')->name('catering_order_status');
        Route::get('one_view_catering_orders', 'CateringOrderController@one_view')->name('one_view_catering_orders');
       

    });


});


	
//SUPPORT
Route::get('clear-cache', 'CacheController@clear_cache');