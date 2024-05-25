<?php

use App\Models\Reservation;
return [

    /*
    |--------------------------------------------------------------------------
    | Pickup Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during admin side bar items for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'item_already_exist_in_cart' => 'Selected item is already added in the cart',
    'item_added_in_cart' => 'Selected item has been added into the cart successfully',
    'quantity_updated' => 'Quantity has been updated successfully',
    'pickup_cart_not_found' => 'Pickup cart not found',
    'order_placed_successfully' => 'Pickup order has been placed successfully',
    'pickup_not_allowed' => 'Pickup not allowed for selected branch',
    'non_working_day' => 'Restaurant is not working on this day',
    'slots_not_available' => 'Slots are not available',
    'available_slots' => 'Available Slots',
    'payment_mode_invalid' => 'Selected payment mode is invalid',
    'insufficient_wallet_balance' => 'You have not sufficient ballance in your e wallet',
    'your_cart_detail' => 'Your cart details',
    'cart_empty' => 'Your cart is empty',
    'its_past_hours' => 'Time should not be past',
    'item_not_exist' => 'Cart item does not exist',

    'status' => [
      'received' => 'Received',
      'confirmed' => 'Confirmed',
      'cancelled' => 'Cancelled',
      'ready_for_pickup' => 'Ready for Pickup',
      'picked_up' => 'Picked Up',
    ],

    'status_for_api' => [
      'received' => 'Order Received',
      'received_message' => 'Your order has been placed successfully.',
      'confirmed' => 'Order Confirmed',
      'confirmed_message' => 'Restaurant has Confirmed your order. Food is now being Prepared.',
      'cancelled' => 'Order Cancelled',
      'cancelled_message' => 'Your order has been cancelled.',
      'ready_for_pickup' => 'Order Ready For Pickup',
      'ready_for_pickup_message' => 'Order is ready for pickup.',
      'picked_up' => 'Order Picked Up',
      'picked_up_message' => 'Order has been Delivered.',
    ],

    'order_tracking_details' => 'Pickup order tacking details',
    'order_not_found' => 'Pickup order not found',
    'heading' => 'Pickup Order',
    'w_title' => 'Pickup Order List',
    'customer' => 'Name',
    'contact' => 'Contact',
    'date' => 'Date & Time',
    'token' => 'Token No',
    'status_updated' => 'Status updated successfully',
    'error' => 'Status not updated',
    'details' => 'Details',
    'id' => 'Id',
    'chairs' => 'Persons Booked',
    'cancel' => 'Cancel',
    'served' => 'Served',
    'order_id' => 'Pickup Order ID',
    'show' => 'Pickup order details',
    'details' => 'Details',
    'not_allowed_notification_or_status' => 'Status can be updated only for today\'s pickups',

];
