<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->bigInteger('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            $table->string('status')->nullable();
            $table->string('moderation_status')->nullable();
            $table->text('moderation_status_comment')->nullable();
            $table->bigInteger('provider_product_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->text('meta')->nullable();
            $table->text('canvas_meta')->nullable();
            $table->tinyInteger('was_declined')->nullable();
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
        Schema::dropIfExists('products');
    }
}
