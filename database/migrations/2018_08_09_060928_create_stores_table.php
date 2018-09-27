<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->string('provider')->nullable();
            $table->string('domain')->nullable();
            $table->bigInteger('deleted_at')->nullable();
            $table->string('inserts')->nullable();
            $table->bigInteger('provider_store_id')->nullable();
            $table->string('name')->nullable();
            $table->string('website')->nullable();
            $table->string('token')->nullable();
            $table->string('secret')->nullable();
            
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
        Schema::dropIfExists('stores');
    }
}
