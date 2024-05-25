<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRestBranchesTableAddWlSeats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rest_branches', function($table) {
            $table->integer('waiting_list_seats')->nullable()->after('total_seats');
            $table->integer('reservable_seats')->nullable()->after('waiting_list_seats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('waiting_list_seats');
        $table->dropColumn('reservable_seats');
    }
}
