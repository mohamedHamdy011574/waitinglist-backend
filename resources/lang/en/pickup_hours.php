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
    'singular' => 'Pickup Hour',
    'plural' => 'Pickup Hours',
    'heading' => 'Pickup Hour Management',
    'title' => 'Pickup Hours List',
    'add_new' => 'Add New Pickup Hours List',
    'details' => 'Pickup Hour Details',
    'update' => 'Update Pickup Hour',
    'edit' => 'Edit Pickup Hour',
    'show' => 'Show Pickup Hour',

    //attributes
    'pickup_hours' => 'Pickup Hours',
    'shifts' => 'Shifts',
    'time_of_shift' => 'Time Of Shift',
    'allowed_chair' => 'Allowed Chair/HR',
    'shift_name' => 'Shift Name',
    'shift_time' => 'Shift Time',
    'from_time' => 'Shift From Time',
    'to_time' => 'Shift To Time',
    'pickup_slot_duration' => 'Pickup Slot Duration (In Mins)',
    'pickup_slot_duration_error' => 'Pickup slot duration should not be greater than from and to time difference.',
    'pickup_slot_duration_error_not_zero' => 'Pickup slot duration should not be 0.',

    //messages
    'added' => 'Pickup Hours Added Successfully',
    'updated' => 'Pickup Hours Updated Successfully',
    'deleted' => 'Pickup Hours Deleted Successfully',
    'error' => 'Something went wrong',
    'status_updated' => 'Pickup Hours Status Updated Successfully',
    'cant_inactive' => 'Pickup Hours Can not be Deactive, because Some Vendors subscribed this package',
    'already_exists' => 'This Pickup Hours Already Exists',
    'start_and_end_time_should_be_include_in_business_working_hour' => 'Shift from and to time should be within business working hour',
    'overlaping_with_other_shift' => 'Pickup hours are overlapping with other shift.',

];