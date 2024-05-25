<?php

use Illuminate\Database\Seeder;
use App\Models\Concern;
use App\Models\Translations\ConcernTranslation;
use App\Models\GeneralConfiguration;

class ConcernTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $concernArray = [
        	['status' => 'active' ],
        	['status' => 'active' ],
        	['status' => 'active' ],
            ['status' => 'active' ],
        ];

    		foreach ($concernArray as $key => $value) {
    			$concern = Concern::create($value);
                foreach(config('app.locales') as $lk=>$lv)
                {
                    if($key == 0)
                    {
                        $concern_trans_array = ['concern_id' => $concern->id, 'concern'=>'It Contains Offensive Materials', 'locale' => $lk ];
                    }
                    elseif($key == 1)
                    {
                        $concern_trans_array = ['concern_id' => $concern->id, 'concern'=>"It's Off Topic", 'locale' => $lk ];
                    }
                    elseif($key == 2)
                    {
                        $concern_trans_array = ['concern_id' => $concern->id, 'concern'=>'It Looks Like Spam', 'locale' => $lk ];
                    }
                    else
                    {
                        $concern_trans_array = ['concern_id' => $concern->id, 'concern'=>'Something Else', 'locale' => $lk ];
                    }
                    ConcernTranslation::create($concern_trans_array);
                }
        }
    }
}
