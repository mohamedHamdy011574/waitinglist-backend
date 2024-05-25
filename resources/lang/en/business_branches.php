<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Branches Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during admin side bar items for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    //titles
    'singular' => 'Branch',
    'plural' => 'Branches',
    'heading' => 'Branch Management',
    'title' => 'Branch List',
    'add_new' => 'Add New Branch',
    'details' => 'Branch Details',
    'update' => 'Update Branch',
    'edit' => 'Edit Branch',
    'show' => 'Show Branch',

    //messages
    'added' => 'Branch Added Successfully',
    'updated' => 'Branch Updated Successfully',
    'deleted' => 'Branch Deleted Successfully',
    'error' => 'Something went wrong',
    'status_updated' => 'Branch Status Updated Successfully',
    'invalid_time' => 'To time can not be earlier then From time',
    'business_details_required' => 'Business Details Must Required',
    
    //attributes
    'restaurant' => 'Restaurant',
    'country' => 'Country',
    'state' => 'State',
    'branch_name' => 'Branch Name',
    'branch_email' => 'Branch Email',
    'branch_phone_number' => 'Branch Phone Number',
    'payment_options' => 'Payment Options',
    'address' => 'Branch Address',
    'select_restaurant' => 'Select restaurant',
    'select_country' => 'Select country',
    'select_state' => 'Select state',
    'pickups_per_hour' => 'Pickups per Hour',
    'reservation' => 'Reservation',
    'waiting_list' => 'Waiting List',
    'pickup' => 'Pickup',
    'catering' => 'Catering',
    'food_truck' => 'Food Truck',
    'branch_type' => 'Branch Type',
    'service_type' => 'Service Type',
    'select_branch_type' => 'Select branch type',
    'cash_payment' => 'Cash Payment',
    'online_payment' => 'K-NET Payment',
    'wallet_payment' => 'E-Wallet Payment',
    //timings
    'week_day' => 'Day',
    'week_days' => [
            'select' => 'Select a day',
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ],
    'on' => 'On',    
    'off' => 'Off',    
    'from' => 'From',
    'to' => 'To',
    'for_all' => 'For All',
    'capacity' => 'Capacity',
    'choose_payment_option' => 'Please Select Payment Option',
    'delivery_info' => 'Delivery Info',
    'min_notice' => 'Min. Notice (in Hours)',
    'min_order' => 'Min. Order (in :currency)',
    'delivery_charge' => 'Delivery Charge',
    'by_area' => 'By Area',

    'no_waiting_list_service' => 'Waiting list service is not allowed in this restaurant',
    'no_working_day' => 'Restaurant is not working today',
    'no_working_hours' => 'Restaurant is not working this time',
    'number_of_people_invalid' => 'Invalid number of people, Maximum allowed number of people is :allowed_chairs',
    'restaurant_closed_now' => 'Restaurant is not working at this moment, please check in working hours',
    
];
