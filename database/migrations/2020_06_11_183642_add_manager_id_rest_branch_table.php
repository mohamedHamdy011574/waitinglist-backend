<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagerIdRestBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rest_branches', function($table) {
           $table->bigInteger('manager_id')->unsigned()->nullable()->after('restaurant_id');
           $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rest_branches', function($table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
        }); 
    }
}
