<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBusinessWorkingHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_working_hours', function(Blueprint $table)
        {
            $table->boolean('sunday_serving')->default(false)->after('business_id');
            $table->boolean('monday_serving')->default(false)->after('sunday_serving');
            $table->boolean('tuesday_serving')->default(false)->after('monday_serving');
            $table->boolean('wednesday_serving')->default(false)->after('tuesday_serving');
            $table->boolean('thursday_serving')->default(false)->after('wednesday_serving');
            $table->boolean('friday_serving')->default(false)->after('thursday_serving');
            $table->boolean('saturday_serving')->default(false)->after('friday_serving');
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
