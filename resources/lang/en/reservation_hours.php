<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    //titles
    'singular' => 'Reservation Hour',
    'plural' => 'Reservation Hours',
    'heading' => 'Reservation Hour Management',
    'title' => 'Reservation Hours List',
    'add_new' => 'Add New Reservation List',
    'details' => 'Reservation Hour Details',
    'update' => 'Update Reservation Hour',
    'edit' => 'Edit Reservation Hour',
    'show' => 'Show Reservation Hour',

    //attributes
    'reservation_hours' => 'Reservation Hours',
    'shifts' => 'Shifts',
    'time_of_shift' => 'Time Of Shift',
    'allowed_chair' => 'Allowed Chairs/Shift',
    'shift_name' => 'Shift Name',
    'shift_time' => 'Shift Time',
    'from_time' => 'Shift From Time',
    'to_time' => 'Shift To Time',
    'dining_slot_duration' => 'Dining Slot Duration (In Mins)',
    'dining_slot_duration_error' => 'Dining slot duration should not be greater than from and to time difference.',
    'dining_slot_duration_error_not_zero' => 'Dining slot duration should not be 0.',

    //messages
    'added' => 'Reservation Hours Added Successfully',
    'updated' => 'Reservation Hours Updated Successfully',
    'deleted' => 'Reservation Hours Deleted Successfully',
    'error' => 'Something went wrong',
    'status_updated' => 'Reservation Hours Status Updated Successfully',
    'cant_inactive' => 'Reservation Hours Can not be Deactive, because Some Vendors subscribed this package',
    'already_exists' => 'This Reservation Hours Already Exists',
    'start_and_end_time_should_be_include_in_business_working_hour' => 'Shift from and to time should be within business working hour',
    'overlaping_with_other_shift' => 'Reservation hours are overlapping with other shift.',

];