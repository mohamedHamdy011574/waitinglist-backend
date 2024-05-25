<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Guest;
use App\Models\Setting;
use App\Models\Country;
use App\Models\State;
use DB;

class UserResource extends JsonResource
{   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'first_name' => ucfirst($this->first_name),
        'last_name' => ucfirst($this->last_name),
        'email' => $this->email,
        'phone_number' => $this->phone_number,
        'birth_date' => ($this->birth_date == '1970-01-01') ? '' : $this->birth_date,
        'country' => ($this->country_id) ? new CountryResource(Country::find($this->country_id)) : ["id" => '', "name" => ""],
        'city' => ($this->city_id) ? new StateResource(State::where('country_id', $this->country_id)->first()) : ["id" => '', 'country_id' => ($this->country_id) ? ($this->country_id) : '', "name" => ""],
        'currency' => Setting::get('currency'),
        'gems' => $this->gems,
        'e_wallet_amount' => $this->e_wallet_amount,
        'blogger' => ($this->blogger) ? new BloggerResource($this->blogger) : [
                                        "id" => '',
                                        "customer_id" =>  '',
                                        "blogger_photo" =>  "",
                                        "blogger_name" =>  "",
                                      ],
        'preferred_language' => $this->preferred_language,                              
      ];
    }
}
