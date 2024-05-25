<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBusinessBranchTableAddCateringDeliveryInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_branches', function($table) {
          $table->string('min_notice_unit')->default('hours')->after('pickups_per_hour');
          $table->integer('min_notice')->nullable()->after('min_notice_unit');
          $table->decimal('min_order',8,3)->nullable()->default(0)->after('min_notice');
          $table->string('delivery_charge')->default('by_area')->after('min_notice');
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
