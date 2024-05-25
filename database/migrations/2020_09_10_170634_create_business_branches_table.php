<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->unsigned();
            $table->enum('branch_type',['restaurant','food_truck','catering'])->default('restaurant');
            $table->string('branch_email')->nullable();
            $table->string('branch_phone_number')->nullable();
            $table->boolean('reservation_allow')->default(false);
            $table->boolean('waiting_list_allow')->default(false);
            $table->boolean('pickup_allow')->default(false);
            $table->boolean('cash_payment_allow')->default(false);
            $table->boolean('online_payment_allow')->default(false);
            $table->boolean('wallet_payment_allow')->default(false);
            $table->integer('pickups_per_hour')->default(0);
            $table->text('address')->nullable();
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('business_branch_translations', function(Blueprint $table) 
        {
            $table->increments('id');
            $table->bigInteger('business_branch_id')->unsigned();
            $table->string('branch_name')->nullable();
            $table->string('locale')->index();
            $table->unique(['business_branch_id', 'locale']);
            $table->foreign('business_branch_id')->references('id')->on('business_branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_branch_translations');
        Schema::dropIfExists('business_branches');
    }
}
