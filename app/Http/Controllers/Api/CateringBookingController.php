<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CateringCart;
use App\Models\CateringAddonCart;
use App\Models\CateringCartItem;
use App\Models\CateringAddonCartItem;
use App\Models\CateringOrder;
use App\Models\CateringOrderItem;
use App\Models\CateringAddon;
use App\Models\CateringOrderAddress;
use App\Models\CateringAddonOrder;
use App\Models\CateringAddonOrderItem;
use App\Models\PickupHour;
use App\Models\CateringPackage;
use App\Models\BusinessBranch;
use App\Models\Coupon;
use App\Models\Business;
use App\Models\BusinessWorkingHour;
use App\Http\Resources\CateringCartResource;
use App\Http\Resources\CateringOrderResource;
use App\Http\Resources\CateringOrderResourceForOrder;
use App\Http\Resources\CateringAddonCartResource;
use DB,Validator,Auth;
use App\Models\Helpers\RestaurantHelpers;
use App\Models\Helpers\PickupHelpers;
use Carbon\Carbon;
use App\Models\WalletLog;
use App\Models\Helpers\CommonHelpers;
use App\Models\Setting;


class CateringBookingController extends BaseController
{	
    use RestaurantHelpers, PickupHelpers, CommonHelpers;

    public function view_catering_cart(Request $request)
    {
        $customer_id = Auth::user()->id;
        $cart = CateringCart::where('customer_id',$customer_id)->first();
        if(!$cart)
        {
            return $this->sendError('', trans('catering_orders.cart_empty'));
        }
        return $this->sendResponse(new CateringCartResource($cart), trans('catering_orders.your_cart_detail'));   
    }

    /**
    * Add to Cart
    *
    * @return JSON
    */
	public function add_to_cart_for_catering(Request $request) 
    {
        // print_r($request->all()); die;
        // die;
        try{
            DB::beginTransaction();
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'business_branch_id' => 'required|numeric',
                'cat_packg_id'            => 'required|numeric',
                'quantity'           => 'required|numeric',
                'coupon_id'          => 'numeric|nullable',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            //validate restaurant branch
            $catering_branch = BusinessBranch::where(['id' => $request->business_branch_id, 'branch_type' => 'catering', 'status' => 'active'])->first();
            if(!$catering_branch) {
                return $this->sendError('',trans('catering.not_found')); 
            }
            //validate restaurant menu
            $catering_package = CateringPackage::where('id',$request->cat_packg_id)->where('business_id',$catering_branch->business_id)->where('status','active')->first();
            if(!$catering_package) {
                return $this->sendError('',trans('catering_packages.not_found')); 
            }

            $customer_id = Auth::user()->id;

            //initiate new cart
            $cart = new CateringCart;

            //check if cart is already exist
            $existing_cart = CateringCart::where('customer_id', $customer_id)->first();

            if($existing_cart)
            {
                $cart = $existing_cart;

                //delete existing cart if business branch is not same as in existing cart and initiate new cart
                if($existing_cart->business_branch_id != $request->business_branch_id)
                {
                    $existing_cart->delete();
                    $cart = new CateringCart;
                }
            }

            //check if item already in cart
            $menu_exist = 0;
            if($existing_cart)
            {
                $cart_item = CateringCartItem::where('catering_cart_id',$existing_cart->id)->where('cat_packg_id',$request->cat_packg_id)->first();
                // update cart if item already exist and quantity has been passed from app
                if($cart_item)
                {
                    // if(!isset($request->quantity))
                    // {
                    //     return $this->sendError('',trans('catering_orders.item_already_exist_in_cart'));
                    // }
                    $quantity = $request->quantity;
                    $old_quantity = $cart_item->quantity;                                     

                    //update cart
                    $existing_cart->quantity = ($existing_cart->quantity - $old_quantity) + $quantity;
                    $existing_cart->sub_total = ( $existing_cart->sub_total - ($cart_item->unit_price * $old_quantity) ) + ( $quantity * $catering_package->price );
                    $existing_cart->grand_total = $existing_cart->grand_total();
                    $existing_cart->save();                    

                    //else update cart item
                    $cart_item->quantity = $request->quantity;
                    $cart_item->unit_price = $catering_package->price;
                    $cart_item->save();

                    //remove cart item if quantity is 0
                    if($request->quantity == 0)
                    {
                        // echo $cart_item->id; die;
                        if($cart_item->catering_addon_cart){
                            $addons = @$cart_item->catering_addon_cart->catering_addon_cart_items;
                            // print_r($addons);die;
                            foreach (@$addons as $addon) {
                                $addon_request = new \Illuminate\Http\Request(); 
                                $addon_request->replace([
                                    'catering_cart_item_id' => $cart_item->id,
                                    'catering_addon_id' => $addon['cat_addon_id'],
                                    'quantity'  => 0,
                                ]);
                                $this->add_to_cart_for_catering_addon($addon_request);
                                /*$this->save_addon_price_in_cart_item($cart_item->id);
                                $this->save_addon_price_in_cart($catering_cart_item->catering_cart_id);*/     
                            }
                        }

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
                    $cart = CateringCart::where('customer_id', $customer_id)->first();
                    
                    // ADD ADDONS IN CART ITEMS
                    if($cart) {
                        // echo 'Cart che';
                        foreach ($cart->catering_cart_items as $c_itm) {
                            // echo '<br>150 '. count($cart->catering_cart_items); 
                            // echo '<br>151 '. $cart->quantity; 
                            // echo '<br>153 '. count($request->addons); 
                            // die;
                            if($c_itm->cat_packg_id == $request->cat_packg_id){
                                for ($i=1; $i <= $c_itm->quantity; $i++) { 
                                    if($request->addons){
                                        foreach (@$request->addons as $r_addon) {
                                            // for ($a=1; $a <= $r_addon['quantity']; $a++) { 
                                                $addon_request = new \Illuminate\Http\Request(); 
                                                $addon_request->replace([
                                                    'catering_cart_item_id' => $c_itm->id,
                                                    'catering_addon_id' => $r_addon['catering_addon_id'],
                                                    'quantity'  => (int)$r_addon['quantity'],
                                                    // 'quantity'  => (int)$c_itm->quantity*$r_addon['quantity'],
                                                ]);
                                                $this->add_to_cart_for_catering_addon($addon_request);
                                            // }
                                        }
                                    }
                                }
                            }
                        }
                    }



                    if(!$cart)
                    {
                        return $this->sendResponse('',trans('catering_orders.cart_empty'));
                    }

                    $cart = CateringCart::find($cart->id);
                    return $this->sendResponse(new CateringCartResource($cart),trans('catering_orders.quantity_updated'));
                }
            }

            // echo '<pre>'; print_r($existing_cart); die;
            // if(isset($request->quantity))
            // {
            //     return $this->sendError('',trans('catering_orders.item_not_exist')); 
            // }
            $discount = 0;
            $cart->customer_id        = $customer_id;
            $cart->business_branch_id = $request->business_branch_id;
            $cart->coupon_id          = $request->coupon_id;
            $cart->item_count        += 1;
            $cart->quantity          += $request->quantity;
            $cart->sub_total         += ($request->quantity * $catering_package->price);
            $cart->discount           = $discount;
            $cart->grand_total        = $cart->grand_total();
            $cart->save();

            $cart_items = [
                'catering_cart_id' => $cart->id,
                'cat_packg_id'     => $catering_package->id,
                'package_title'     => $catering_package->package_name,
                'quantity'       => $request->quantity,
                'unit_price'     => $catering_package->price,
                'special_request'  => @$request->special_request,
            ];
            CateringCartItem::create($cart_items);


            // ADD ADDONS IN CART ITEMS
            if($cart) {
                // echo 'Cart che';
                foreach ($cart->catering_cart_items as $c_itm) {
                    // echo '<br>150 '. count($cart->catering_cart_items); 
                    // echo '<br>151 '. $cart->quantity; 
                    // echo '<br>153 '. count($request->addons); 
                    // die;
                    if($c_itm->cat_packg_id == $request->cat_packg_id){
                        for ($i=1; $i <= $c_itm->quantity; $i++) { 
                            if($request->addons){
                                foreach (@$request->addons as $r_addon) {
                                    // for ($a=1; $a <= $r_addon['quantity']; $a++) { 
                                        $addon_request = new \Illuminate\Http\Request(); 
                                        $addon_request->replace([
                                            'catering_cart_item_id' => $c_itm->id,
                                            'catering_addon_id' => $r_addon['catering_addon_id'],
                                            'quantity'  => (int)$r_addon['quantity'],
                                            // 'quantity'  => (int)$c_itm->quantity*$r_addon['quantity'],
                                        ]);
                                        $this->add_to_cart_for_catering_addon($addon_request);
                                    // }
                                }
                            }
                        }
                    }
                }
            }


            //remove cart if no items
            // if($request->quantity == 0)
            //     if(count($cart->catering_cart_items) == 0){
            //         $cart->delete();
            //     }
            // }

            DB::commit();
            $cart = CateringCart::find($cart->id);
            return $this->sendResponse(new CateringCartResource($cart),trans('catering_orders.item_added_in_cart'));
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
    public function available_times_for_catering(Request $request) {
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
                return $this->sendError('',trans('catering_orders.its_old_date'));
            }

            $catering_branch = BusinessBranch::where(['id' => $request->business_branch_id, 'branch_type' => 'catering',  'status' => 'active'])->first();
            if(!$catering_branch) {
                return $this->sendError('',trans('catering.not_found')); 
            }

            // if(!$catering_branch->catering_allow) {
            //     return $this->sendError('',trans('catering_orders.pickup_not_allowed'));
            // }

            // DATE RESERVABLE ?
            $weekday = date('N', strtotime($request->date));
            $working_days = BusinessWorkingHour::where('business_id', $catering_branch->business_id)->first();
            if(!$working_days) {
              return $this->sendError('',trans('catering_orders.non_working_day'));
            }
            $working_days_array = $this->get_working_days($working_days);

            if(!in_array($weekday, $working_days_array)) {
              return $this->sendError('',trans('catering_orders.non_working_day'));
            }
            //CATERER IS AVAILABLE ?
            $caterer = Business::where(['id' => $catering_branch->business_id, 'status' => 'active'])->first();
            if(!$caterer) {
              return $this->sendError('',trans('catering.not_found')); 
            }

            if($request->date == date('Y-m-d') && $caterer->working_status != 'available') {
              return $this->sendError('',trans('catering.bookings_disabled')); 
            }

            $requestArr = $request->all();
            $allowed_pickup_per_hour = $catering_branch->pickups_per_hour;
            $available_slots = $this->getAvailableTimeSlots($requestArr,$caterer->id,$allowed_pickup_per_hour);
            $available_time_slots_p1 = [];
            foreach ($available_slots as $kres => $vres) {
                $res_time = [];
                $res_time['time'] = $kres;
                $res_time['send_time'] = $vres;
                array_push($available_time_slots_p1, $res_time);
            }

            DB::commit();
            if($available_time_slots_p1){        
                return $this->sendResponse($available_time_slots_p1, trans('catering_orders.available_slots'));
            } else {
                return $this->sendError('', trans('catering_orders.slots_not_available'));
            }  
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }
    }

    /**
    * Place order for pickup
    *
    * @return JSON
    */
    public function place_order_for_catering(Request $request) 
    {
        try{
            DB::beginTransaction();
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'business_branch_id' => 'required|numeric',
                'catering_cart_id'   => 'required|numeric',
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
                    return $this->sendError('',trans('catering_orders.its_past_hours'));
                }
            }            

            // validate id business branch is exist
            $catering_branch = BusinessBranch::where(['id' => $request->business_branch_id, 'status' => 'active'])->first();
            if(!$catering_branch) {
                return $this->sendError('',trans('catering.not_found')); 
            }

            // if(!$catering_branch->pickup_allow) {
            //     return $this->sendError('',trans('catering_orders.pickup_not_allowed'));
            // }

            // DATE RESERVABLE ?
            $weekday = date('N', strtotime($request->date));
            $working_days = BusinessWorkingHour::where('business_id', $catering_branch->business_id)->first();
            if(!$working_days) {
              return $this->sendError('',trans('catering_orders.non_working_day'));
            }
            $working_days_array = $this->get_working_days($working_days);

            if(!in_array($weekday, $working_days_array)) {
              return $this->sendError('',trans('catering_orders.non_working_day'));
            }

            //CATERER IS AVAILABLE ?
            $caterer = Business::where(['id' => $catering_branch->business_id, 'status' => 'active'])->first();
            if(!$caterer) {
              return $this->sendError('',trans('catering.not_found')); 
            }

            if($request->date == date('Y-m-d') && $caterer->working_status != 'available') {
              return $this->sendError('',trans('catering.bookings_disabled')); 
            }

            //validate payment mode
            if($request->payment_mode != 'cod' && $request->payment_mode != 'e_wallet')
            {
                return $this->sendError('',trans('catering_orders.payment_mode_invalid')); 
            }

            // validate cart
            $catering_cart = CateringCart::find($request->catering_cart_id);

            if(!$catering_cart) {
                return $this->sendError('',trans('catering_orders.catering_cart_not_found')); 
            }

            //validate if slot is available
            $catering_branch = BusinessBranch::find($request->business_branch_id);
            $requestArr = $request->all();
            $allowed_pickup_per_hour = $catering_branch->pickups_per_hour;
            $available_slots = $this->getAvailableTimeSlots($requestArr,$catering_branch->business_id,$allowed_pickup_per_hour);
            if(!in_array(date('H:i:s',strtotime($request->time)), $available_slots))
            {
                return $this->sendError('',trans('catering_orders.slots_not_available')); 
            }
            if($request->coupon_id)
            {
                $coupon = Coupon::where('id',$request->coupon_id)
                        ->where('active','1')
                        ->where('start_date', '<=', date('Y-m-d H:i:s'))  
                        ->where('end_date', '>=', date('Y-m-d H:i:s'))
                        ->first();
                // print_r($coupon);die;
                if(!$coupon)
                {
                    return $this->sendError('',trans('coupons.not_found'));
                }
                // die;
                $catering_cart->coupon_id = $request->coupon_id;
                $catering_cart->discount = ($catering_cart->sub_total * $coupon->discount) / 100;
                $catering_cart->grand_total = $catering_cart->grand_total();
                $catering_cart->save();
            }
            // validate e wallet ballance if payment mode is e_wallet
            if($request->payment_mode == 'e_wallet')
            {
                if(Auth::user()->e_wallet_amount < $catering_cart->grand_total)
                {
                    return $this->sendError('',trans('catering_orders.insufficient_wallet_balance')); 
                }
            }
            // Create the order from the cart
            $input = $request->all();
            $catering_order = $this->saveOrderFromCartForApi($input, $catering_cart);

            if($catering_order)
            {
                // update user wallet if payment mode is e_wallet
                if($request->payment_mode == 'e_wallet')
                {
                    User::where('id',Auth::user()->id)->decrement('e_wallet_amount',$catering_order->grand_total);

                    $wallet_history_data = [
                        'customer_id' => Auth::user()->id,
                        'amount' => $catering_order->grand_total,
                        'type' => 'removed',
                    ];
                    $walletLog    = WalletLog::create($wallet_history_data);
                    //send notification
                    $title = trans('gems.notification_title');
                    $body = trans('gems.notification_body_for_removed',['amount' => Setting::get('currency').' '.$walletLog->amount]);
                    $nres = $this->sendNotification(Auth::user(),$title,$body,"e_wallet_balance","");
                }
                // Delete the cart and notify customer
                $catering_cart->delete();
                // $user = Auth::user();
                // $user->notify(new OrderPlaced($order,$user));
            }
            DB::commit();

            return $this->sendResponse(new CateringOrderResource($catering_order),trans('catering_orders.order_placed_successfully'));
        } catch (\Exception $e)
        {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }
    }

    public function order_details($order_id){
        if(CateringOrder::find($order_id)){
            return $this->sendResponse(new CateringOrderResource(CateringOrder::find($order_id)),trans('catering_orders.order_details'));
        } else {
            return $this->sendError([],trans('catering_orders.order_not_found'));
        }
    }

    public function orders() {
        $orders = CateringOrder::where('customer_id',Auth::user()->id)->latest()->paginate();
        return $this->sendPaginateResponse(CateringOrderResource::collection($orders),trans('catering_orders.w_title'));
    }

    /**
     * Create a new pickup order from the cart
     *
     * @param  Request $request
     * @param  App\PicupCart $cart
     *
     * @return App\CateringOrder
     */
    function saveOrderFromCartForApi($request, $catering_cart)
    {
        try{
            DB::beginTransaction();

            // Save order from cart
            $pickup_slot_duration = 60;
            $catering_order = new CateringOrder;
            $catering_cart_data = $catering_cart->toArray();
            // echo "<pre>";print_r($catering_cart_data);exit;
            $payment_status = 'paid';        
            $order_status = 'booked';
            $catering_order->fill(
                array_merge($catering_cart_data, [
                    'first_name'     => Auth::user()->first_name,
                    'phone_number'   => Auth::user()->phone_number,
                    'grand_total'    => $catering_cart->grand_total(),
                    'order_date' => $request['date'].' '.$request['time'],
                    'due_date' => $request['date'],
                    'due_time' => $request['time'],
                    'end_time' => date("H:i", strtotime('+'.($pickup_slot_duration - date('i',strtotime($request['time']))).' minutes', strtotime($request['time']))),
                    'payment_mode'   => $request['payment_mode'],
                    'payment_status' => $payment_status,
                    'order_status'   => $order_status
                ])
            );
            $catering_order->save();

            //save order items from cart items
            $catering_cart_items = CateringCartItem::where('catering_cart_id',$request['catering_cart_id'])->get();

            foreach ($catering_cart_items as $cart_item) {

                $order_item = [
                    'catering_order_id' => $catering_order->id,
                    'cat_packg_id'    => $cart_item->cat_packg_id,
                    'package_title'      => $cart_item->package_title,
                    'quantity'        => $cart_item->quantity,
                    'addons_price'        => $cart_item->addons_price,
                    'unit_price'      => $cart_item->unit_price,
                    'special_request'  => @$request->special_request,
                ];

                $order_item_inserted = CateringOrderItem::create($order_item);

                if($order_item_inserted) {

                    //SAVING ADDON CART
                    $addon_cart = $cart_item->catering_addon_cart;
                    if($addon_cart){
                        
                        $addon_order_data = [
                        'catering_order_item_id' => $order_item_inserted->id,
                        'item_count' => $addon_cart->item_count,
                        'quantity' => $addon_cart->quantity,
                        'total' => $addon_cart->total,
                        ];
                        $addon_order_inserted = CateringAddonOrder::create($addon_order_data);

                        if($addon_order_inserted) {
                            //SAVING ADDON CART ITEMS   
                            $addon_cart_items =   $addon_cart->catering_addon_cart_items;
                            foreach ($addon_cart_items as $acitem) {

                                $addon_order_items_data = [
                                    'catering_addon_order_id' => $addon_order_inserted->id,
                                    'cat_addon_id' => $acitem->cat_addon_id,
                                    'quantity' => $acitem->quantity,
                                    'unit_price' => $acitem->unit_price,
                                ];
                                $addon_order_item_inserted = CateringAddonOrderItem::create($addon_order_items_data); 
                            }
                        } 
                    }
                    

                    
                }

            }


            //save address
            $catering_address = CateringOrderAddress::where('cart_id', $request['catering_cart_id'])->first();
            if($catering_address) {
               $catering_address->order_id =  $catering_order->id;
               $catering_address->save();
            }

            DB::commit();
            return $catering_order;

        } catch (\Exception $e)
        {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }
    }

    public function getAvailableTimeSlots($requestArr,$business_id,$allowed_pickup_per_hour)
    {

        $pickup_slot_duration = 60; //in mins

        //getting all pickup hours
        $catering_hours = BusinessWorkingHour::where('business_id',$business_id)->get();
        // echo "<pre>";print_r($catering_hours->toArray());exit;
        $pkp_hrs_arr = array();
        foreach ($catering_hours as $pkp_hr) {
            $rbranch_working_from = $pkp_hr->from_time;
            $rbranch_working_to = date("H:i:s", strtotime($pkp_hr->to_time) - ($pickup_slot_duration * 60));

            $pkp_hrs_arr[$pkp_hr->id] = $this->splitTime($requestArr['date'].' '.$rbranch_working_from, $requestArr['date'].' '.$rbranch_working_to, $pickup_slot_duration);

        }
        // exit;
        $time_slots = array();
        // echo "<pre>";print_r($catering_hours);exit;
        foreach ($pkp_hrs_arr as $res_hrs) {
            foreach ($res_hrs as $pkp_key => $res_value) {
                $time_slots[$pkp_key] = $res_value;
            }
        }
        ksort($time_slots);        
        // echo "<pre>asdas";print_r($time_slots);exit;
        $booked_time_range = CateringOrder::where([
                                      'business_branch_id' => $requestArr['business_branch_id'],
                                      'due_date' => $requestArr['date'],
                                  ])
                                  ->whereIn('order_status', ['booked'])
                                  ->select(['order_date', 'due_date', 'due_time', 'end_time'])
                                  ->orderBy('order_date')
                                  ->get()
                                  ->toArray();
        $booked_time_slots = [];
        foreach ($booked_time_range as $rte) {
            $slots = []; 
            $due_time = date("H:i", strtotime('-'.(date('i',strtotime($rte['due_time']))).' minutes', strtotime($rte['due_time'])));
            $slots =  $this->splitTimeRezerved($requestArr['date'].' '.$due_time, $requestArr['date'].' '.$rte['end_time'], $pickup_slot_duration);
            foreach ($slots as $s => $v) {
                $booked_time_slots[$s] = $v;             
            }
        }
        $available_time_slots = [];
        foreach($time_slots as $kts=>$vts){
            if(!array_key_exists($kts, $booked_time_slots)){
                $available_time_slots[$vts] = $kts;
            }
        }
        return $available_time_slots;
    }

        /**
    * Add to Cart Addon
    *
    * @return JSON
    */
    public function add_to_cart_for_catering_addon(Request $request) {
        // try{
            DB::beginTransaction();
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'catering_cart_item_id' => 'required|numeric',
                'catering_addon_id'            => 'required|numeric',
                'quantity'           => 'required|numeric',
            ]);
            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }
            $customer_id = Auth::user()->id;
            $catering_cart_item = CateringCartItem::where(['id' => $request->catering_cart_item_id])->first();
            if(!$catering_cart_item) {
                return $this->sendError('',trans('catering_orders.catering_cart_item_not_found'));
            }
            $catering_addon = CateringAddon::where(['id' => $request->catering_addon_id])->first();
            if(!$catering_addon) {
                return $this->sendError('',trans('catering_orders.catering_addon_not_found'));
            }            
            //validate restaurant menu
            $catering_package = CateringPackage::where('id',$catering_cart_item->cat_packg_id)->first();
            if(!$catering_package) {
                return $this->sendError('',trans('catering_packages.not_found')); 
            }
            $customer_id = Auth::user()->id;
            $addon_cart = new CateringAddonCart;
            //initiate new cart
            $existing_addon_cart = CateringAddonCart::where(['catering_cart_item_id' => $request->catering_cart_item_id])->first();
            if($existing_addon_cart){
                $addon_cart = $existing_addon_cart;
            }
            if($existing_addon_cart) {
                // echo('699');
                $addon_cart_item = CateringAddonCartItem::where('catering_addon_cart_id',$existing_addon_cart->id)->where('cat_addon_id',$request->catering_addon_id)->first();
                // echo '<pre>'; print_r($addon_cart_item); die;

                if($addon_cart_item) {
                    // die('same to sam');
                    $quantity = $request->quantity;
                    $old_quantity = $addon_cart_item->quantity;

                    //update cart
                    $existing_addon_cart->quantity = ($existing_addon_cart->quantity - $old_quantity) + $quantity;
                    $existing_addon_cart->total = ( $existing_addon_cart->total - ($addon_cart_item->unit_price * $old_quantity) ) + ( $quantity * $catering_addon->addon_rate );
                    $existing_addon_cart->save();  

                    //else update cart item
                    $addon_cart_item->quantity = $request->quantity;
                    $addon_cart_item->unit_price = $catering_addon->addon_rate;
                    $addon_cart_item->save();

                    // SAVE ADDON PRICE IN CART ITEM
                    /*$catering_cart_item->addons_price = ( $existing_addon_cart->total - ($addon_cart_item->unit_price * $old_quantity) ) + ( $quantity * $catering_package->price );
                    $catering_cart_item->save();*/
                    $this->save_addon_price_in_cart_item($catering_cart_item->id);

                    $this->save_addon_price_in_cart($catering_cart_item->catering_cart_id);             
                    //remove cart item if quantity is 0
                    if($request->quantity == 0){
                        $addon_cart_item->delete();
                        $existing_addon_cart->item_count = $existing_addon_cart->item_count - 1;
                        $existing_addon_cart->save();
                        //delete cart if cart item is the only item in the cart
                        if($existing_addon_cart->item_count == 0){
                            $existing_addon_cart->delete();
                        }
                        // SAVE ADDON PRICE IN CART ITEM
                        $catering_cart_item->addons_price = 0;
                        $catering_cart_item->save();
                        $this->save_addon_price_in_cart_item($catering_cart_item->id);
                        $this->save_addon_price_in_cart($catering_cart_item->catering_cart_id);
                    } 
                    DB::commit();
                    $addon_cart = CateringAddonCart::where('catering_cart_item_id', $request->catering_cart_item_id)->first();

                    if(!$addon_cart){
                        return $this->sendResponse('',trans('catering_orders.cart_empty'));
                    }
                    return $this->sendResponse(new CateringAddonCartResource($addon_cart),trans('catering_orders.quantity_updated'));
                }
            }

            $addon_cart->catering_cart_item_id  = $request->catering_cart_item_id;
            $addon_cart->item_count        += 1;
            $addon_cart->quantity          += $request->quantity;
            $addon_cart->total         += ($request->quantity * $catering_addon->addon_rate);
            $addon_cart->save();

            // SAVE ADDON PRICE IN CART ITEM
            // $catering_cart_item->addons_price = ($request->quantity * $catering_package->price);
            // $catering_cart_item->save();
            $this->save_addon_price_in_cart_item($catering_cart_item->id);
            $this->save_addon_price_in_cart($catering_cart_item->catering_cart_id);

            $addon_cart_items = [
                'catering_addon_cart_id' => $addon_cart->id,
                'cat_addon_id'     => $catering_addon->id,
                'quantity'       => $request->quantity,
                'unit_price'     => $catering_addon->addon_rate,
            ];
            CateringAddonCartItem::create($addon_cart_items);
            DB::commit();
            return $this->sendResponse(new CateringAddonCartResource($addon_cart),trans('catering_orders.item_added_in_cart'));
        /*} catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('',$e->getMessage());
        }*/
    }

    public function save_addon_price_in_cart($catering_cart_id){
        $catering_cart = CateringCart::find($catering_cart_id);
        $addons_total = 0;
        if($catering_cart) {
            foreach($catering_cart->catering_cart_items as $citem) {
                $addons_total += $citem->addons_price;
            }
        }
        $catering_cart->addons_total = $addons_total;
        $catering_cart->grand_total = ($catering_cart->sub_total + $addons_total + $catering_cart->taxes - $catering_cart->discount);
        $catering_cart->save();
    }
    public function save_addon_price_in_cart_item($catering_cart_item_id){
        $catering_cart_item = CateringCartItem::find($catering_cart_item_id);
        $addons_price = 0;
        if($catering_cart_item) {
            if($catering_cart_item->catering_addon_cart){
                $addons_price += $catering_cart_item->catering_addon_cart->total;
            }
        }
        $catering_cart_item->addons_price = $addons_price;
        $catering_cart_item->save();
    }


    public function catering_order_address(Request $request) {
        $validator=  Validator::make($request->all(),[
            'cart_id'   => 'required|exists:catering_carts,id',
            'city_id'   => 'required|exists:states,id',
            'block'   => 'required|max:100',
            'street'   => 'required|max:100',
            'avenue'   => 'required|max:100',
            'house_bulding'   => 'required|max:100',
            'floor'   => 'required|max:100',
            'apartment_number'   => 'required|max:100',
            'mobile_number'   => 'required|max:13',
        ]);
        if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());
        }

        $data = $request->all();
        // print_r($data); die;

        $existing_catering_address = CateringOrderAddress::where('cart_id', $request->cart_id)->first();
        if($existing_catering_address) {
            $existing_catering_address->update($data);  
            $catering_address = $existing_catering_address;  
        }else{
            $catering_address = CateringOrderAddress::create($data);
        }
        if($catering_address) {
            return $this->sendResponse('', trans('catering_orders.address_saved'));
        }else{
            return $this->sendError(trans('catering_orders.address_not_saved')); 
        }
    }
}
