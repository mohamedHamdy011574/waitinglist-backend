<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->text('profile_pic')->nullable()->after('user_type');
            $table->date('birth_date')->default('1970-01-01')->after('profile_pic');
            $table->integer('country_id')->unsigned()->nullable()->after('birth_date');
            $table->enum('status',['active','inactive'])->default('active')->after('country_id');
            $table->boolean('verified')->default(false)->after('status');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
