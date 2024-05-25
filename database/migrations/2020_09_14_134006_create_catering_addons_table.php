<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_addons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->decimal('addon_rate',8,3)->default(0.00);
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('catering_addon_translations', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->bigInteger('catering_addon_id')->unsigned();
            $table->string('addon_name')->nullable();
            $table->string('locale')->index();
            $table->unique(['catering_addon_id', 'locale']);
            $table->foreign('catering_addon_id')->references('id')->on('catering_addons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catering_addon_translations');
        Schema::dropIfExists('catering_addons');
    }
}
