<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\GeneralConfiguration;
class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $settingArray = [
        	['name'=>'from_email', 'value' => 'admin@admin.com', 'status' => 'active' ],
        	['name'=>'contact_no', 'value' => '+91 1234567890', 'status' => 'active' ],
        	['name'=>'facebook_url', 'value' => 'www.facebook.coms', 'status' => 'active' ],
            ['name'=>'twitter_url', 'value' => 'www.twitter.com', 'status' => 'active' ],
            ['name'=>'instagram_url', 'value' => 'www.instagram.com', 'status' => 'active' ],
            ['name'=>'site_name', 'value' => 'Waiting List', 'status' => 'active' ],
            ['name'=>'google_API_key', 'value' => 'AIzaSyCPRQ6zO6df5JlMRnLvEAAqfg1UWw_T8Os', 'status' => 'active' ],
            ['name'=>'currency', 'value' => 'KD', 'status' => 'active' ],
            ['name'=>'fcm_server_key', 'value' => 'AAAAKBhApIU:APA91bHI2lGjW6acRUyV_VDEWI0wQs9QrYcicYCXYBvM-xRa0cnktiIkbjORQMN_DI7ikVPXkwSNbR20_k1shDVfr4ADF8C4qAynppMqMZsv5DUm_QPtEOThY-C9Mg_325It1sJgDoSS', 'status' => 'active' ],
            // ['name'=>'Dining_Slot_Duration', 'value' => '45', 'status' => 'active' ],
            ['name'=>'default_sort_by', 'value' => 'alphabetical', 'status' => 'active' ],
            ['name'=>'default_order', 'value' => 'asc', 'status' => 'active' ],
            ['name'=>'currency_exchange_rate', 'value' => '20', 'status' => 'active' ],
            ['name'=>'gems_earning_per_ad', 'value' => '10', 'status' => 'active' ],
            ['name'=>'gems_earning_per_sponsor_ad', 'value' => '15', 'status' => 'active' ],
            ['name' => 'smsGateway_url', 'value' => 'https://www.smsbox.com/SMSGateway/Services/Messaging.asmx/Http_SendSMS', 'status' => 'active'],
            ['name' => 'smsGateway_username', 'value' => 'almansour1991', 'status' => 'active'],
            ['name' => 'smsGateway_password', 'value' => 'abdulrahman', 'status' => 'active'],
            ['name' => 'smsGateway_custId', 'value' => '2461', 'status' => 'active'],
            ['name' => 'smsGateway_senderText', 'value' => 'SMSBOX.COM', 'status' => 'active'],
        ];

    		foreach ($settingArray as $key => $value) {
          if(Setting::where('name', $value['name'])->get()->count()){
            Setting::where('name', $value['name'])->update($value);
          }else{
    			 Setting::create($value);
          }
        }
    }
}