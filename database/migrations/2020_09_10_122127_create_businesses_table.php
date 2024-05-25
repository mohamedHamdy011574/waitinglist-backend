<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('vendor_id')->unsigned()->nullable();
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('brand_email')->nullable();
            $table->string('brand_phone_number')->nullable();
            $table->text('link')->nullable();
            $table->string('brand_logo')->nullable();
            $table->boolean('reservation_status')->default(true);
            $table->boolean('waiting_list_status')->default(true);
            $table->boolean('pickup_status')->default(true);
            $table->enum('working_status', ['busy','available','closed','orders_suspended'])->default('available');
            $table->timestamps();
        });

        Schema::create('business_translations', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->unsigned();
            $table->string('brand_name')->nullable();
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['business_id', 'locale']);
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_translations');
        Schema::dropIfExists('businesses');
    }
}
