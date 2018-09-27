<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogAttributeOptionsTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('catalog_attribute_options', function(Blueprint $table) {
      
        $table->increments('id');
        $table->integer('parent_id')->nullable()->index();
        $table->integer('lft')->nullable()->index();
        $table->integer('rgt')->nullable()->index();
        $table->integer('depth')->nullable();
  
        // custom columns 
        $table->string('name');
        $table->string('value');
        $table->integer('attribute_id')->unsigned()->nullable();
        $table->foreign('attribute_id')->references('id')->on('catalog_attributes');
      
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('catalog_attribute_options');
  }

}
