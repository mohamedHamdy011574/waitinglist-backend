<?php

use Illuminate\Database\Seeder;
use App\Models\CompanyDetail;

class CompanyDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $companyDetails = [
        ['name'=>'company_logo', 'value' => '-',],
        ['name'=>'company_name', 'value' => 'WaitingList'],
        ['name'=>'GSTIN', 'value' => '1234r454'],
        ['name'=>'contact_number', 'value' => '+96598989898'],
        ['name'=>'company_email', 'value' => 'info@waitinglist.com'],
        ['name'=>'customer_care_email', 'value' => 'help@waitinglist.com'],
        ['name'=>'company_address', 'value' => 'Kuwait'],
        ['name'=>'country', 'value' => 'Kuwait'],
      ];

      foreach ($companyDetails as $key => $value) {
        CompanyDetail::create($value);
          }
      }
}
