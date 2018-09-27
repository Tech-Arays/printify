<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Store;
use App\Models\StoreSettings;
class CreateStoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->increments('id');
            
            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            
            $table->boolean('auto_orders_confirm');
            $table->boolean('import_unsynced')->nullable();
            $table->boolean('notify_unsynced')->nullable();
            $table->boolean('auto_stock_update')->nullable();
            $table->boolean('auto_push_products')->nullable();
            $table->boolean('card_charge_limit_enabled')->nullable();
            $table->decimal('card_charge_limit_amount')->nullable();
            $table->decimal('card_charge_charges_amount')->nullable();
            $table->engine = 'InnoDB';
        });
        
        $stores = Store::all();
        foreach ($stores as $store) {
            StoreSettings::createForStoreIfNotExists($store->id);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_settings');
    }
}
