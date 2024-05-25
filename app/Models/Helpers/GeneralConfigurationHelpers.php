<?php

namespace App\Models\Helpers;

use App\Models\GeneralConfiguration;

trait GeneralConfigurationHelpers
{

    public function get_free_training_sheet(){
        return GeneralConfiguration::where('name', 'free_training_sheet')->value('value');
    }

    public function get_product_posting_price(){
        return GeneralConfiguration::where('name', 'product_posting_price')->value('value');
    }

}
