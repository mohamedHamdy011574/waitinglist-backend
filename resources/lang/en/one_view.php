<?php

use App\Models\Reservation;
return [

    /*
    |--------------------------------------------------------------------------
    | Admin Cms Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during admin side bar items for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    //title
    'singular' => 'Reservation',
    'plural' => 'One View',
    'heading' => 'One View',
    'title' => 'Reservation List',
    'add_new' => 'New Reservation',
    'details' => 'Reservation Details',
    'update' => 'Update Reservation',
    'edit' => 'Edit Reservation',
    'show' => 'Show Reservation',
  

    //attributes
    'reservation' => 'Reservations',
    'waiting_list' => 'Waiting List',
    'pick_up' => 'Pickup Orders',
    'upcoming_reservation' => 'Upcoming Reservation',
    'no_reservation' => 'No Reservations found',
    'reservation_id' => 'Reservation ID',
    'name'  => 'Name',
    'phone_number'  => 'Contact',
    'persons_booked'  => 'Persons Booked',
    'date'  => 'Date',
    'cancel'  => 'Cancel',
    'served'  => 'Served',
    'token_no' => 'Token No',
    'pickup_order_id' => 'Pickup Order ID',
    'catering_order_id' => 'Catering Order ID',
    'address' => 'Address',

    'upcoming_orders' => 'Upcoming Orders',
    'in_progress' => 'In Progress',
    'ready_for_pickup' => 'Ready For Pickup',
    'ordered' => 'Ordered',
    'sub_total' => 'Sub total',
    'grand_total' => 'Grand total',
    'addons_total' => 'Addons total',


    
    

    //message
    'added' => 'Reservation has been done Successfully',
    'cancelled' => 'Reservation has been cancelled Successfully',
    'deleted' => 'Reservation has been deleted Successfully',
    'updated' => 'Reservation has been updated Successfully',
    'error' => 'Something went wrong',
    'status_updated' => 'Status Updated Successfully',
    'customer_status_update_successfully' => 'Reservation Status Update Successfully',
    'customer_status_update_unsuccessfully' => 'Reservation Status Update UnSuccessfully',
    'already_reserved' => 'You have already reserved a table for this restaurant for :date :time',


    //API
    'non_working_day' => 'Restaurant is not working on this day',
    'invalid_no_of_people' => 'Number of peopple is invalid',
    'available_slots' => 'Available Slots',
    'slots_not_available' => 'Slots are not available',
    'invalid_seating_area' => 'Invalid Seating Area',
    'not_booked_by_you' => 'You can cancel only your reservation',
    'old_reservation_cant_deleted' => 'Old reservation can not be deleted',
    'old_reservation_cant_cancelled' => 'Old reservation can not be cancelled',
    'reservation_already_cancelled' => 'Your reservation already cancelled',

    'status' => [
      Reservation::CONFIRMED_STATUS => 'Confirmed',
      Reservation::CANCELLED_STATUS => 'Cancelled',
      Reservation::CHECKED_IN_STATUS => 'Checked In',
      Reservation::CHECKED_OUT_STATUS => 'Checked Out',
    ],

    'not_found' => 'Reservation not found',
    'success' => 'Reservation history got successfully',
    'details_success' => 'Reservation details got successfully',
    'reservation_not_allowed' => 'Selected branch does not allow reservation.',
    'invalid_coupon' => 'Coupon is not valid.',
 
];
