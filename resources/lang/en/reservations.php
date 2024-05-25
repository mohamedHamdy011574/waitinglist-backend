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
    'plural' => 'Reservations',
    'heading' => 'Reservation Management',
    'title' => 'Reservation List',
    'add_new' => 'New Reservation',
    'details' => 'Reservation Details',
    'update' => 'Update Reservation',
    'edit' => 'Edit Reservation',
    'show' => 'Show Reservation',
  

    //attributes
    'name' => 'Reservation Name',
    'code' => 'Reservation Code',
    'description' => 'Description',
    'discount' => 'Discount (In %)',
    'check_in_date'  => 'Check in date',
    'customer' => 'Customer',
    'select_customer' => 'Select Customer',
    'restaurants' => "Restaurant",
    'select_restaurant' => "Select Restaurant",
    'rest_branches' => "Restaurant Branch",
    'select_rest_branch' => "Select Restaurant Branch",
    'no_of_persons' => "No. of Persons",
    'seats_not_available' => "Only :seats seats are available at the moment in your selected branch",
    'seating_areas' => "Seating Areas",
    

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
    'non_working_day'                => 'Restaurant is not working on this day',
    'invalid_no_of_people'           => 'Number of peopple is invalid',
    'available_slots'                => 'Available Slots',
    'slots_not_available'            => 'Slots are not available',
    'invalid_seating_area'           => 'Invalid Seating Area',
    'not_booked_by_you'              => 'You can cancel only your reservation',
    'old_reservation_cant_deleted'   => 'Old reservation can not be deleted',
    'old_reservation_cant_cancelled' => 'Old reservation can not be cancelled',
    'reservation_already_cancelled'  => 'Your reservation already cancelled',
    'no_of_chairs_not_available'     => 'The number of chairs you have chosen is not available',
    'chairs_not_enough'              => 'There is no enough chairs at this time.',

    'status' => [
      Reservation::CONFIRMED_STATUS   => 'Confirmed',
      Reservation::CANCELLED_STATUS   => 'Cancelled',
      Reservation::CHECKED_IN_STATUS  => 'Checked In',
      Reservation::CHECKED_OUT_STATUS => 'Checked Out',
    ],

    'not_found' => 'Reservation not found',
    'success' => 'Reservation history got successfully',
    'details_success' => 'Reservation details got successfully',
    'reservation_not_allowed' => 'Selected branch does not allow reservation.',
    'invalid_coupon' => 'Coupon is not valid.',
 
];
