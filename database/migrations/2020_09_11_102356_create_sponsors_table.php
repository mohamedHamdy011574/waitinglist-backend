<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('logo')->nullable();
            $table->text('video')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->date('duration_from')->nullable();
            $table->date('duration_to')->nullable();
            $table->dateTime('notified_at')->nullable();
            $table->decimal('ad_price',8,3)->default(0.00);
            $table->enum('payment_mode', ['ewallet', 'payment_gateway'])->default('payment_gateway');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sponsors');
    }
}
