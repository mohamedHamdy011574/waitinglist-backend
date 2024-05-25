<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('for_restaurant')->default(false);
            $table->boolean('for_catering')->default(false);
            $table->boolean('for_food_truck')->default(false);
            $table->boolean('reservation')->default(false);
            $table->boolean('waiting_list')->default(false);
            $table->boolean('pickup')->default(false);
            $table->integer('branches_include')->default(1);
            $table->integer('subscription_period')->default(1);
            $table->decimal('package_price',8,3)->default(0.00);
            $table->string('currency')->default('KD');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('sub_package_translations', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('sub_package_id')->unsigned();
            $table->string('package_name')->nullable();
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['sub_package_id', 'locale']);
            $table->unique(['package_name', 'locale']);
            $table->foreign('sub_package_id')->references('id')->on('subscription_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_packages');
    }
}
