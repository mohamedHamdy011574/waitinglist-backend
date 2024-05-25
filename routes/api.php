<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/



Route::post('auth/login', 'Api\Auth\AuthController@login');
Route::post('auth/register', 'Api\Auth\AuthController@signup');
Route::post('auth/mobile_activation', 'Api\Auth\AuthController@mobile_activation');
Route::post('auth/resend_otp', 'Api\Auth\AuthController@resend_otp');
Route::post('auth/forgot_password', 'Api\Auth\AuthController@forgot_password');
Route::post('auth/reset_password', 'Api\Auth\AuthController@reset_password');

Route::get('cuisines', 'Api\CuisineController@index');
Route::post('restaurants', 'Api\RestaurantController@index');
Route::post('restaurant', 'Api\RestaurantController@details');
// Route::get('restaurant/menu/{restaurant_id}', 'Api\RestaurantController@menu');
Route::get('restaurant_branches/{restaurant_id}', 'Api\RestaurantBranchController@index');

Route::post('catering', 'Api\CateringController@index');
Route::get('catering/{id}', 'Api\CateringController@details');
Route::get('catering_packages/{business_id}/{category_id}', 'Api\CateringController@packages');


Route::post('food_trucks', 'Api\FoodTruckController@index');
Route::post('food_truck', 'Api\FoodTruckController@details');
// Route::get('food_truck/menu/{food_truck_id}', 'Api\FoodTruckController@menu');

//Restaurant menu categories & Menu Itms
  Route::get('menu_categories/{business_id}', 'Api\MenuController@menu_categories');
  Route::post('menu_items', 'Api\MenuController@menu_items');

Route::post('news', 'Api\NewsController@index');
Route::get('news/{news_id}', 'Api\NewsController@details');
Route::get('cities/{country_id?}', 'Api\CityController@index');
Route::get('countries', 'Api\CountryController@index');

//Food Bloggers
Route::post('bloggers', 'Api\BloggerController@index');
Route::get('cms/{page_name?}','Api\CmsController@page');
Route::post('food_blogs', 'Api\BloggerController@food_blog_index');
Route::get('food_blog_detail/{id?}', 'Api\BloggerController@food_blog_detail');



Route::get('how_to', 'Api\HowToController@index');


Route::middleware(['auth:api'])->group( function() {
  Route::post('become_blogger', 'Api\BloggerController@become_blogger');
  Route::post('food_blog_add', 'Api\BloggerController@food_blog');
  Route::post('food_blog_edit', 'Api\BloggerController@food_blog_update');
  Route::get('food_blog_delete/{id?}', 'Api\BloggerController@food_blog_delete');
  Route::post('my_food_blogs', 'Api\BloggerController@my_food_blog');
  Route::get('user', 'Api\UserController@show');
  Route::post('user/update', 'Api\UserController@update_user');
  Route::post('user/change_password', 'Api\Auth\AuthController@change_password');
  Route::post('auth/logout', 'Api\Auth\AuthController@logout');

  //my favorties News
  Route::get('news/add_to_favorites/{news_id}', 'Api\NewsController@add_to_favorites');
  Route::get('news/remove_from_favorites/{news_id}', 'Api\NewsController@remove_from_favorites');
  Route::post('favorite_news', 'Api\NewsController@favorites');

  //my favorite Food Blogs
  Route::get('food_blog/add_to_favorites/{foodblog_id?}', 'Api\BloggerController@add_to_favorites');
  Route::get('food_blog/remove_from_favorites/{foodblog_id?}', 'Api\BloggerController@remove_from_favorites');
  Route::post('favorite_food_blogs', 'Api\BloggerController@favorite_blogs');


  //my favorties Restauants
  Route::get('restaurants/add_to_favorites/{business_id}', 'Api\RestaurantController@add_to_favorites');
  Route::get('restaurants/remove_from_favorites/{business_id}', 'Api\RestaurantController@remove_from_favorites');
  Route::post('favorite_restaurants', 'Api\RestaurantController@favorites');

  //my Favorite Caterers
  Route::get('catering/add_to_favorites/{business_id}', 'Api\CateringController@add_to_favorites');
  Route::get('catering/remove_from_favorites/{business_id}', 'Api\CateringController@remove_from_favorites');

  Route::post('catering_ratings', 'Api\CateringController@ratings');

  //my favorties Food Trucks
  Route::get('food_trucks/add_to_favorites/{food_truck_id}', 'Api\FoodTruckController@add_to_favorites');
  Route::get('food_trucks/remove_from_favorites/{food_truck_id}', 'Api\FoodTruckController@remove_from_favorites');
  Route::post('favorite_food_trucks', 'Api\FoodTruckController@favorites');

  // notification
  Route::post('notifications', 'Api\NotificationController@notification');
  Route::get('delete_notifications', 'Api\NotificationController@delete_notifications');
  Route::get('notifications_unread_count', 'Api\NotificationController@notification_count');
  Route::get('user/lang/{lang?}', 'Api\UserController@preferred_language');

  Route::post('coupons', 'Api\RestaurantController@coupons');

  //Restaurant working_days
  // Route::get('working_days/{restaurant_branch_id?}', 'Api\RestaurantController@working_days');
  Route::get('restaurant_booking_info/{business_branch_id?}', 'Api\RestaurantController@working_days');

  //Reservation related apis
  Route::post('available_times', 'Api\ReservationController@available_times');
  Route::post('reservation', 'Api\ReservationController@reservation');
  Route::post('reservation/cancel', 'Api\ReservationController@cancel');
  Route::post('reservation/history', 'Api\ReservationController@reservation_history');
  Route::get('reservation/history_monthly', 'Api\ReservationController@reservation_history_mw');
  // Route::post('reservation/history', 'Api\ReservationController@reservation_history_mw');
  Route::get('reservation/{id}', 'Api\ReservationController@reservation_detail');


  //Sponsors
  Route::post('become_sponsor', 'Api\SponsorController@become_sponsor');
  Route::get('sponsors', 'Api\SponsorController@sponsors');

  //Advertisements
  Route::get('advertisements', 'Api\AdvertisementController@advertisements');

  //Reviews
  Route::post('blog_reviews', 'Api\ReviewController@index');
  Route::post('add_review', 'Api\ReviewController@add_review');  
  Route::post('report_review', 'Api\ReviewController@report_review');
  Route::get('concerns', 'Api\ReviewController@concern_list');

  //Gems & Wallet
  Route::post('get_gems_by_watch_ad', 'Api\GemsController@get_gems_by_watch_ad');
  Route::post('convert_gems_to_wallet', 'Api\GemsController@convert_gems_to_wallet');
  Route::get('wallet_history', 'Api\WalletController@wallet_history');
  Route::get('gems_to_wallet_exchange_rate', 'Api\GemsController@gems_to_wallet_exchange_rate');

  //Pickup
  Route::post('add_to_cart_for_pickup', 'Api\PickupController@add_to_cart_for_pickup');
  Route::post('available_times_for_pickup', 'Api\PickupController@available_times_for_pickup');
  Route::post('place_order_for_pickup', 'Api\PickupController@place_order_for_pickup');
  Route::get('view_pickup_cart', 'Api\PickupController@view_pickup_cart');
  Route::get('track_pickup_order/{pickup_order_id}', 'Api\PickupController@track_pickup_order');

  //catering order
  Route::post('add_to_cart_for_catering', 'Api\CateringBookingController@add_to_cart_for_catering');
  Route::post('available_times_for_catering', 'Api\CateringBookingController@available_times_for_catering');
  Route::post('place_order_for_catering', 'Api\CateringBookingController@place_order_for_catering');
  Route::get('view_catering_cart', 'Api\CateringBookingController@view_catering_cart');  
  Route::post('add_to_cart_for_catering_addon', 'Api\CateringBookingController@add_to_cart_for_catering_addon');  
  
  Route::post('catering_order_address', 'Api\CateringBookingController@catering_order_address');  
  Route::get('catering_order_details/{order_id}', 'Api\CateringBookingController@order_details');  
  // Route::get('catering_orders', 'Api\CateringBookingController@orders');  

  //waiting List
  Route::get('current_waiting_list/{busines_branch_id}', 'Api\WaitingListController@current_waiting_list');
  Route::post('add_me_on_waitinglist', 'Api\WaitingListController@add_me_on_waitinglist');

  // Terms and condition
  Route::get('terms_and_conditions/{business_id}', 'Api\CmsController@terms_and_conditions');

  //Test
  Route::post('send_notification', 'Api\TestController@send_notification');
});