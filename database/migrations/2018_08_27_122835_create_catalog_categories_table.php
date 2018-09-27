<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogCategoriesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('catalog_categories', function(Blueprint $table) {
      // These columns are needed for Baum's Nested Set implementation to work.
      // Column names may be changed, but they *must* all exist and be modified
      // in the model.
      // Take a look at the model scaffold comments for details.
      // We add indexes on parent_id, lft, rgt columns by default.
      $table->increments('id');
      $table->integer('parent_id')->nullable()->index();
      $table->integer('lft')->nullable()->index();
      $table->integer('rgt')->nullable()->index();
      $table->integer('depth')->nullable();
      $table->boolean('is_available_for_generator')->nullable();
      $table->string('slug')->nullable()->unique();
      $table->string('name')->nullable()->unique();
      $table->bigInteger('preview_file_id')->unsigned()->nullable()->change();
      $table->decimal('prepaid_amount', 10, 2)->nullable();
      // Add needed columns here (f.ex: name, slug, path, etc.)
      // $table->string('name', 255);

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('catalog_categories');
  }

}
