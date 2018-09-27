<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductModelTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_model_templates', function (Blueprint $table) {
            $table->increments('id');    
            $table->string('name')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('visibility')->nullable();
            $table->string('product_title')->nullable();
            $table->string('product_description')->nullable();
            $table->string('mockup_format')->nullable(); 
            $table->integer('category_id')->unsigned()->nullable();
            //$table->foreign('category_id')->references('id')->on('product_categories')->unsigned()->index()->onDelete('cascade'); 
            $table->bigInteger('overlay_file_id')->unsigned()->nullable();
           // $table->foreign('overlay_file_id')->references('id')->on('files')->unsigned()->index()->onDelete('set null');
            $table->bigInteger('overlay_back_file_id')->unsigned()->nullable();
            //$table->foreign('overlay_back_file_id')->references('id')->on('files')->unsigned()->index()->onDelete('set null');
            $table->bigInteger('example_file_id')->unsigned()->nullable();
           // $table->foreign('example_file_id')->references('id')->on('files')->unsigned()->index()->onDelete('set null');
            $table->integer('garment_id')->unsigned()->nullable();
            //$table->foreign('garment_id')->references('id')->on('garments')->unsigned()->index()->onDelete('set null');
            $table->bigInteger('image_back_file_id')->unsigned()->nullable();
            //$table->foreign('image_back_file_id')->references('id')->on('files')->unsigned()->index()->onDelete('set null');
            $table->bigInteger('image_file_id')->unsigned()->nullable();
           // $table->foreign('image_file_id')->references('id')->on('files')->unsigned()->index();
            $table->bigInteger('preview_file_id')->unsigned()->nullable();
            //$table->foreign('preview_file_id')->references('id')->on('files')->unsigned()->index();
            $table->char('sku', 100)->nullable();
            $table->decimal('weight', 12, 3);
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
        Schema::dropIfExists('product_model_templates');
    }
}
