<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_models', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('template_id')->unsigned()->nullable();
            $table->string('inventory_status', 20)->nullable();
            $table->decimal('price_back', 10, 2)->nullabe();
            $table->decimal('price_both', 10, 2)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('product_models');
    }
}
