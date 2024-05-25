<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReservationHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservation_hours', function(Blueprint $table)
        {
            $table->integer('dining_slot_duration')->default(0)->after('allowed_chair');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservation_hours', function(Blueprint $table)
        {
            $table->dropColumn('dining_slot_duration');
        });
    }
}
