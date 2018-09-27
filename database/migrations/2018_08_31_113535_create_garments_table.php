<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGarmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garments', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('slug')->nullable();
            $table->string('name')->nullable();
            $table->integer('position')->unsigned()->nullable();
            
            $table->integer('garment_group_id')->unsigned()->nullable();
            $table->foreign('garment_group_id')->references('id')->on('garment_groups');
            
            $table->bigInteger('preview_file_id')->unsigned()->nullable();
            $table->foreign('preview_file_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garments');
    }
}
