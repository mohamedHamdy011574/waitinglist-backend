<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PickupCart;
use App\Models\PickupCartItem;
use App\Models\PickupOrder;
use App\Models\PickupOrderItem;
use App\Models\PickupHour;
use App\Models\RestaurantMenu;
use App\Models\BusinessBranch;
use App\Models\Coupon;
use App\Models\Business;
use App\Models\BusinessWorkingHour;
use App\Models\WalletLog;
use App\Http\Resources\PickupCartResource;
use App\Http\Resources\PickupOrderResource;
use DB,Validator,Auth;
use App\Models\Helpers\RestaurantHelpers;
use App\Models\Helpers\PickupHelpers;
use Carbon\Carbon;
use App\Models\Helpers\CommonHelpers;
use App\Models\Setting;

class PickupController extends BaseController
{	
    use RestaurantHelpers, PickupHelpers, CommonHelpers;

    public function view_pickup_cart(Request $request)
    {
        $customer_id = Auth::user()->id;
        $cart = PickupCart::where('customer_id',$customer_id)->first();
        if(!$cart)
        {
            return $this->sendError('', trans('pickups.cart_empty'));   
        }
        return $this->sendResponse(new PickupCartResource($cart), trans('pickups.your_cart_detail'));   
    }

    /**
    * Add to Cart
    *
    * @return JSON
    */
	public function add_to_cart_for_pickup(Request $request) 
    {
        try{
            DB::beginTransaction();
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'business_branch_id' => 'required|numeric',
                'menu_id'            => 'required|numeric',
                'quantity'           => 'numeric|nullable',
                'coupon_id'          => 'numeric|nullable',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            //validate restaurant branch
            $restBranch = BusinessBranch::where(['id' => $request->business_branch_id, 'status' => 'active'])->first();
            if(!$restBranch) {
                return $this->sendError('',trans('restaurants.not_found')); 
            }
            //validate restaurant menu
            $restMenu = RestaurantMenu::where('id',$request->menu_id)->where('business_id',$restBranch->business_id)->where('status','active')->first();
            if(!$restMenu) {
                return $this->sendError('',trans('restaurant_menus.not_found')); 
            }

            $customer_id = Auth::user()->id;

            //initiate new cart
            $cart = new PickupCart;

            //check if cart is already exist
            $existing_cart = PickupCart::where('customer_id', $customer_id)->first();
            if($existing_cart)
            {
                $cart = $existing_cart;

                //delete existing cart if business branch is not same as in existing cart and initiate new cart
                if($existing_cart->business_branch_id != $request->business_branch_id)
                {
                    $existing_cart->delete();
                    $cart = new PickupCart;
                }
            }

            //check if item already in cart
            $menu_exist = 0;
            if($existing_cart)
            {
                $cart_item = PickupCartItem::where('pickup_cart_id',$existing_cart->id)->where('menu_id',$request->menu_id)->first();
                // update cart if item already exist and quantity has been passed from app
                if($cart_item)
                {
                    if(!isset($request->quantity))
                    {
                        return $this->sendError('',trans('pickups.item_already_exist_in_cart'));
                    }
                    $quantity = $request->quantity;
                    $old_quantity = $cart_item->quantity;                                     

                    //update cart
                    $existing_cart->quantity = ($existing_cart->quantity - $old_quantity) + $quantity;
                    $existing_cart->sub_total = ( $existing_cart->sub_total - ($cart_item->unit_price * $old_quantity) ) + ( $quantity * $restMenu->price );
                    $existing_cart->grand_total = $existing_cart->grand_total();
                    $existing_cart->save();                    

                    //else update cart item
                    $cart_item->quantity = $request->quantity;
                    $cart_item->unit_price = $restMenu->price;
                    $cart_item->save();

                    //remove cart item if quantity is 0
                    if($request->quantity == 0)
                    {
                        $cart_item->delete();
                        $existing_cart->item_count = $existing_cart->item_count - 1;
                        $existing_cart->save();
                        //delete cart if cart item is the only item in the cart
                        if($existing_cart->item_count == 0)
                        {
                            // echo "<pre>";print_r("this");exit;
                            $existing_cart->delete();
                        }
                    } 
                    DB::commit();
                    $cart = PickupCart::where('customer_id', $customer_id)->first();
                    if(!$cart)
                    {
                        return $this->sendResponse('',trans('pickups.cart_empty'));
                    }
                    return $this->sendResponse(new PickupCartResource($cart),trans('pickups.quantity_updated'));
                }
            }
            if(isset($request->quantity))
            {
                return $this->sendError('',trans('pickups.item_not_exist')); 
            }
            $discount = 0;
            $cart->customer_id        = $customer_id;
            $cart->business_branch_id = $request->business_branch_id;
            $cart->coupon_id          = $request->coupon_id;
            $cart->item_count        += 1;
            $cart->quantity          += 1;
            $cart->sub_total         += $restMenu->price;
            $cart->discount           = $discount;
            $cart->grand_total        = $cart->grand_total();
            $cart->save();

            $cart_items = [
                'pickup_cart_id' => $cart->id,
                'menu_id'        => $restMenu->id,
                'menu_title'     => $restMenu->name,
                'quantity'       => 1,
                'unit_price'     => $restMenu->price
            ];
            PickupCartItem::create($cart_items);

            DB::commit();
            return $this->sendResponse(new PickupCartResource($cart),trans('pickups.item_added_in_cart'));
        } catch (\Exception $e)
        {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }
    }

    /**
    * Availabel times for pickup
    *
    * @return JSON
    */
    public function available_times_for_pickup(Request $request) 
    {
        try{
            DB::beginTransaction();
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'business_branch_id'   => 'required|numeric',
                'date'   => 'required|date|date_format:Y-m-d'
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            //validate if date is past
            if(date('Y-m-d') > $request->date) {
                return $this->sendError('',trans('restaurants.its_old_date'));
            }

            $rest_branch = BusinessBranch::where(['id' => $request->business_branch_id, 'status' => 'active'])->first();
            if(!$rest_branch) {
                return $this->sendError('',trans('restaurants.not_found')); 
            }

            if(!$rest_branch->pickup_allow) {
                return $this->sendError('',trans('pickups.pickup_not_allowed'));
            }

            // DATE RESERVABLE ?
            $weekday = date('N', strtotime($request->date));
            $working_days = BusinessWorkingHour::where('business_id', $rest_branch->business_id)->first();
            if(!$working_days) {
              return $this->sendError('',trans('pickups.non_working_day'));
            }
            $working_days_array = $this->get_working_days($working_days);

            if(!in_array($weekday, $working_days_array)) {
              return $this->sendError('',trans('pickups.non_working_day'));
            }

            //RESTAURANT IS AVAILABLE ?
            $restaurant = Business::where(['id' => $rest_branch->business_id, 'status' => 'active'])->first();
            if(!$restaurant) {
              return $this->sendError('',trans('restaurants.not_found')); 
            }

            if($request->date == date('Y-m-d') && $restaurant->working_status != 'available') {
              return $this->sendError('',trans('restaurants.bookings_disabled')); 
            }

            $requestArr = $request->all();
            $allowed_pickup_per_hour = $rest_branch->pickups_per_hour;
            $available_slots = $this->getAvailableTimeSlots($requestArr,$restaurant->id,$allowed_pickup_per_hour);
            $available_time_slots_p1 = [];
            foreach ($available_slots as $kres => $vres) {
                $res_time = [];
                $res_time['time'] = $vres['start_time'];
                $res_time['send_time'] = $vres['send_start_time'];
                array_push($available_time_slots_p1, $res_time);
            }

            DB::commit();
            if($available_time_slots_p1)
            {        
                return $this->sendResponse($available_time_slots_p1, trans('pickups.available_slots'));
            }
            else
            {
                return $this->sendError('', trans('pickups.slots_not_available'));
            }  
        } catch (\Exception $e)
        {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }
    }

    /**
    * Place order for pickup
    *
    * @return JSON
    */
    public function place_order_for_pickup(Request $request) 
    {
        try{
            DB::beginTransaction();
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'business_branch_id' => 'required|numeric',
                'pickup_cart_id'     => 'required|numeric',
                'coupon_id'          => 'numeric|nullable',
                'payment_mode'       => 'required',
                'date'               => 'required|date|date_format:Y-m-d',
                'time'               => 'required|date_format:H:i:s'
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            //vallidate if date is past
            if(date('Y-m-d') > $request->date) {
                return $this->sendError('',trans('restaurants.its_old_date'));
            }

            //validate if time is not past if date is today
            if(date('Y-m-d') == $request->date)
            {
                if(date('H:i:s') > $request->time) {
                    return $this->sendError('',trans('pickups.its_past_hours'));
                }
            }            

            // validate id business branch is exist
            $rest_branch = BusinessBranch::where(['id' => $request->business_branch_id, 'status' => 'active'])->first();
            if(!$rest_branch) {
                return $this->sendError('',trans('restaurants.not_found')); 
            }

            if(!$rest_branch->pickup_allow) {
                return $this->sendError('',trans('pickups.pickup_not_allowed'));
            }

            // DATE RESERVABLE ?
            $weekday = date('N', strtotime($request->date));
            $working_days = BusinessWorkingHour::where('business_id', $rest_branch->business_id)->first();
            if(!$working_days) {
              return $this->sendError('',trans('pickups.non_working_day'));
            }
            $working_days_array = $this->get_working_days($working_days);

            if(!in_array($weekday, $working_days_array)) {
              return $this->sendError('',trans('pickups.non_working_day'));
            }

            //RESTAURANT IS AVAILABLE ?
            $restaurant = Business::where(['id' => $rest_branch->business_id, 'status' => 'active'])->first();
            if(!$restaurant) {
              return $this->sendError('',trans('restaurants.not_found')); 
            }

            if($request->date == date('Y-m-d') && $restaurant->working_status != 'available') {
              return $this->sendError('',trans('restaurants.bookings_disabled')); 
            }

            //validate payment mode
            if($request->payment_mode != 'cod' && $request->payment_mode != 'e_wallet')
            {
                return $this->sendError('',trans('pickups.payment_mode_invalid')); 
            }

            // validate cart
            $pickup_cart = PickupCart::find($request->pickup_cart_id);

            if(!$pickup_cart) {
                return $this->sendError('',trans('pickups.pickup_cart_not_found')); 
            }

            //validate if slot is available
            $rest_branch = BusinessBranch::find($request->business_branch_id);
            $requestArr = $request->all();
            $allowed_pickup_per_hour = $rest_branch->pickups_per_hour;
            $available_slots = $this->getAvailableTimeSlots($requestArr,$rest_branch->business_id,$allowed_pickup_per_hour);
            if(!array_key_exists(date('H:i:s',strtotime($request->time)), $available_slots))
            {
                return $this->sendError('',trans('pickups.slots_not_available')); 
            }
            else
            {
                $pickup_slot_duration = $available_slots[$request->time]['pickup_slot_duration'];
            }
            if($request->coupon_id)
            {
                $coupon = Coupon::where('id',$request->coupon_id)->where('active','1')->first();
                if(!$coupon)
                {
                    return $this->sendError('',trans('coupons.not_found'));
                }
                $pickup_cart->coupon_id = $request->coupon_id;
                $pickup_cart->discount = ($pickup_cart->sub_total * $coupon->discount) / 100;
                $pickup_cart->grand_total = $pickup_cart->grand_total();
                $pickup_cart->save();
            }
            // validate e wallet ballance if payment mode is e_wallet
            if($request->payment_mode == 'e_wallet')
            {
                if(Auth::user()->e_wallet_amount < $pickup_cart->grand_total)
                {
                    return $this->sendError('',trans('pickups.insufficient_wallet_balance')); 
                }
            }
            // Create the order from the cart
            $input = $request->all();
            $pickup_order = $this->saveOrderFromCartForApi($input, $pickup_cart, $pickup_slot_duration);

            if($pickup_order)
            {
                // update user wallet if payment mode is e_wallet
                if($request->payment_mode == 'e_wallet')
                {
                    User::where('id',Auth::user()->id)->decrement('e_wallet_amount',$pickup_order->grand_total);

                    $wallet_history_data = [
                        'customer_id' => Auth::user()->id,
                        'amount' => $pickup_order->grand_total,
                        'type' => 'removed',
                    ];
                    $walletLog    = WalletLog::create($wallet_history_data);
                    //send notification
                    $title = trans('gems.notification_title');
                    $body = trans('gems.notification_body_for_removed',['amount' => Setting::get('currency').' '.$walletLog->amount]);
                    $nres = $this->sendNotification(Auth::user(),$title,$body,"e_wallet_balance","");
                }
                // Delete the cart and notify customer
                $pickup_cart->delete();
                // $user = Auth::user();
                // $user->notify(new OrderPlaced($order,$user));
            }
            DB::commit();

            return $this->sendResponse(new PickupOrderResource($pickup_order),trans('pickups.order_placed_successfully'));
        } catch (\Exception $e)
        {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }
    }

    /**
     * Create a new pickup order from the cart
     *
     * @param  Request $request
     * @param  App\PicupCart $cart
     *
     * @return App\PickupOrder
     */
    function saveOrderFromCartForApi($request, $pickup_cart, $pickup_slot_duration)
    {
        // Save order from cart
        // $pickup_slot_duration = 60;
        $pickup_order = new PickupOrder;
        $pickup_cart_data = $pickup_cart->toArray();
        // echo "<pre>";print_r($pickup_cart_data);exit;
        $payment_status = 'paid';        
        $order_status = 'received';
        $pickup_order->fill(
            array_merge($pickup_cart_data, [
                'first_name'     => Auth::user()->first_name,
                'phone_number'   => Auth::user()->phone_number,
                'grand_total'    => $pickup_cart->grand_total(),
                'pickup_date' => $request['date'].' '.$request['time'],
                'due_date' => $request['date'],
                'due_time' => $request['time'],
                'end_time' => date("H:i", strtotime('+'.$pickup_slot_duration.' minutes', strtotime($request['time']))),
                'payment_mode'   => $request['payment_mode'],
                'payment_status' => $payment_status,
                'order_status'   => $order_status
            ])
        );
        $pickup_order->save();

        //save order items from cart items
        $pickup_cart_items = PickupCartItem::where('pickup_cart_id',$request['pickup_cart_id'])->get();

        foreach ($pickup_cart_items as $cart_item) {

            $order_item = [
                'pickup_order_id' => $pickup_order->id,
                'menu_id'         => $cart_item->menu_id,
                'menu_title'      => $cart_item->menu_title,
                'quantity'        => $cart_item->quantity,
                'unit_price'      => $cart_item->unit_price,
            ];

            PickupOrderItem::create($order_item);
        }

        return $pickup_order;
    }

    public function getAvailableTimeSlots($requestArr,$business_id,$allowed_pickup_per_hour)
    {

        

        //getting all pickup hours
        $pickup_hours = PickupHour::where('business_id',$business_id)->where('status','active')->get();
        
        $pkp_hrs_arr = array();
        // echo "<pre>";print_r($pickup_hours->toArray());exit;
        foreach ($pickup_hours as $pkp_hr) {
            $rbranch_working_from = $pkp_hr->from_time;
            $rbranch_working_to = date("H:i:s", strtotime($pkp_hr->to_time) - ($pkp_hr->pickup_slot_duration * 60));
            $pkp_hrs_arr[$pkp_hr->id] = $this->splitTime($requestArr['date'].' '.$rbranch_working_from, $requestArr['date'].' '.$rbranch_working_to, $pkp_hr->pickup_slot_duration);

        }
        $time_slots = array();
        foreach ($pkp_hrs_arr as $pkp_hr_id => $res_hrs) {
            foreach ($res_hrs as $pkp_key => $res_value) {
                if($requestArr['date'] == date('Y-m-d')) {
                    if(strtotime($pkp_key) > time())
                    {
                        $time_arr['time'] = $res_value;
                        $time_arr['pickup_slot_duration'] = PickupHour::find($pkp_hr_id)->pickup_slot_duration;
                        $time_slots[$pkp_key] = $time_arr;
                    }
                }
                else
                {
                    $time_arr['time'] = $res_value;
                    $time_arr['pickup_slot_duration'] = PickupHour::find($pkp_hr_id)->pickup_slot_duration;
                    $time_slots[$pkp_key] = $time_arr;
                }
            }
        }
        ksort($time_slots);        
        $booked_time_range = PickupOrder::where([
                                      'business_branch_id' => $requestArr['business_branch_id'],
                                      'due_date' => $requestArr['date'],
                                  ])
                                  ->whereIn('order_status', ['received','confirmed', 'ready_for_pickup'])
                                  ->select(['pickup_date', 'due_date', 'due_time', 'end_time'])
                                  ->orderBy('pickup_date')
                                  ->get()
                                  ->toArray();
        $booked_time_slots = [];
        $min_slot_duration = 15; //in mins
        foreach ($booked_time_range as $rte) {
            $slots = []; 
            $due_time = date("H:i", strtotime('-'.(date('i',strtotime($rte['due_time']))).' minutes', strtotime($rte['due_time'])));
            $slots =  $this->splitTimeRezerved($requestArr['date'].' '.$due_time, $requestArr['date'].' '.$rte['end_time'], $min_slot_duration);
            foreach ($slots as $s => $v) {
                $booked_time_slots[$s] = $v;             
            }
        }
        $available_time_slots = [];
        foreach($time_slots as $kts=>$vts)
        {
            if(!array_key_exists($kts, $booked_time_slots)){
                $time_slot_arr = [];
                $time_slot_arr['send_start_time'] = $kts;
                $time_slot_arr['start_time'] = $vts['time'];
                $time_slot_arr['pickup_slot_duration'] = $vts['pickup_slot_duration'];
                $available_time_slots[$kts] = $time_slot_arr;
            }
            else
            {
                //calculate booked pickups for this($kts) slot
                $booked_pickups = 0;
                foreach ($booked_time_range as $krtr => $vrtr) {
                    if($vrtr['due_time'] == $kts || ($vrtr['due_time'] >= $kts && $vrtr['end_time'] <= date("H:i:s", strtotime('+60 minutes', strtotime($kts)))))
                    {
                        $booked_pickups++;
                    }
                }
                //check if pickups are available for the slot, 
                if($allowed_pickup_per_hour > $booked_pickups)
                {
                    //check if numb of requested seats by user are available
                    $time_slot_arr = [];
                    $time_slot_arr['send_start_time'] = $kts;
                    $time_slot_arr['start_time'] = $vts['time'];
                    $time_slot_arr['pickup_slot_duration'] = $vts['pickup_slot_duration'];
                    $available_time_slots[$kts] = $time_slot_arr;
                }
            }
        }
        // echo "<pre>";print_r($available_time_slots);exit;
        return $available_time_slots;
    }

    public function track_pickup_order($order_id = '') {
        $pickup_order = PickupOrder::find($order_id);
        if($pickup_order) {

           $statues =  [

                [
                    'status' => trans('pickups.status_for_api.received'),
                    'message' => trans('pickups.status_for_api.received_message'),
                    'is_active' => ($pickup_order->order_status == 'received') ? true : false
                ],
                [
                    'status' => trans('pickups.status_for_api.confirmed'),
                    'message' => trans('pickups.status_for_api.confirmed_message'),
                    'is_active' => ($pickup_order->order_status == 'confirmed') ? true : false
                ],
                [
                    'status' => trans('pickups.status_for_api.ready_for_pickup'),
                    'message' => trans('pickups.status_for_api.ready_for_pickup_message'),
                    'is_active' => ($pickup_order->order_status == 'ready_for_pickup') ? true : false
                ],
                [
                    'status' => trans('pickups.status_for_api.picked_up'),
                    'message' => trans('pickups.status_for_api.picked_up_message'),
                    'is_active' => ($pickup_order->order_status == 'picked_up') ? true : false
                ],

            ];
            
            return $this->sendResponse($statues, trans('pickups.order_tracking_details'));
        }else{
            return $this->sendError('', trans('pickups.order_not_found'));
        }
    }
}
