<?php

namespace App\Models\Helpers;

use App\Models\Setting;

trait SettingHelpers
{
    public function get_setting_value($name){
        return Setting::where('name', $name)->value('value');
    }
}
