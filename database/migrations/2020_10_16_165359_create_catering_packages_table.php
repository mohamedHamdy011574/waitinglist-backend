<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->bigInteger('catering_pkg_cat_id')->unsigned(); 
            $table->foreign('catering_pkg_cat_id')->references('id')->on('catering_package_categories')->onDelete('cascade');
            $table->integer('person_serve')->nullable(); 
            $table->decimal('price',8,3)->default(0.00);
            $table->text('setup_time')->nullable();
            $table->text('setup_time_unit')->nullable();
            $table->text('max_time')->nullable();
            $table->text('max_time_unit')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('catering_package_translations', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->bigInteger('catering_pkg_id')->unsigned();
            $table->string('package_name')->nullable();
            $table->text('food_serving')->nullable();
            $table->string('locale')->index();
            $table->unique(['catering_pkg_id','locale']);
            $table->foreign('catering_pkg_id')->references('id')->on('catering_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catering_package');
    }
}
