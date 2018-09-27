<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGarmentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garment_groups', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('slug')->nullable();
            $table->string('name')->nullable();
            $table->integer('position')->unsigned()->nullable();
            
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
        Schema::dropIfExists('garment_groups');
    }
}
