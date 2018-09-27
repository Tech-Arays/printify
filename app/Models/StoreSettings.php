<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class StoreSettings extends Model
{

    // id
    // store_id
    // auto_orders_confirm
    // import_unsynced
    // notify_unsynced
    // auto_stock_update

    const SETTING_AUTO_ORDERS_CONFIRM        = 'auto_orders_confirm';
    const SETTING_IMPORT_UNSYNCED            = 'import_unsynced';
    const SETTING_NOTIFY_UNSYNCED            = 'notify_unsynced';
    const SETTING_AUTO_STOCK_UPDATE          = 'auto_stock_update';
    const SETTING_AUTO_PUSH_PRODUCTS         = 'auto_push_products';
    const SETTING_CARD_CHARGE_LIMIT_ENABLED  = 'card_charge_limit_enabled';
    const SETTING_CARD_CHARGE_LIMIT_AMOUNT   = 'card_charge_limit_amount';
    const SETTING_CARD_CHARGE_CHARGES_AMOUNT = 'card_charge_charges_amount';

    protected $table = 'store_settings';
    public $timestamps = false;

    protected $guarded = [
        'id',
        'store_id'
    ];
    protected $fillable = [
        'auto_orders_confirm',

        // TODO: not used so far
        //'import_unsynced',
        //'notify_unsynced',
        //'auto_stock_update',

        'auto_push_products',
        'card_charge_limit_enabled',
        'card_charge_limit_amount'
    ];

    /************
     * Mutators
     */



    /*********
     * Scopes
     */



    /***********
     * Relations
     */

        public function store()
        {
            return $this->belongsTo(\App\Models\Store::class, 'store_id');
        }

    /***********
     * Checks
     */



    /**********
     * Counters
     */



    /*************
     * Decorators
     */

        public function getShippingAddressFormatted() {
            // TODO: add real data
            return 'mntz<br>19749 DEARBORN ST<br>CHATSWORTH CA 91311<br>United States<br>(818) 351-7181';
        }

    /*********
     * Helpers
     */

        public static function defaultSettings()
        {
            return collect([
                static::SETTING_AUTO_ORDERS_CONFIRM => 1,
                static::SETTING_IMPORT_UNSYNCED => 0,
                static::SETTING_NOTIFY_UNSYNCED => 0,
                static::SETTING_AUTO_STOCK_UPDATE => 0,
                static::SETTING_AUTO_PUSH_PRODUCTS => 1,
                static::SETTING_CARD_CHARGE_LIMIT_ENABLED => 0,
                static::SETTING_CARD_CHARGE_LIMIT_AMOUNT => 0,
                static::SETTING_CARD_CHARGE_CHARGES_AMOUNT => 0
            ]);
        }

    /***********
     * Functions
     */

        public static function findByStoreId($store_id)
        {
            return static::where('store_id', $store_id)
                ->first();
        }

        public static function createForStore($store_id)
        {
            $settings = new static();
            $settings->fill(static::defaultSettings()->toArray());
            $settings->store_id = $store_id;
            $settings->save();
            return $settings;
        }

        public static function createForStoreIfNotExists($store_id)
        {
            $settings = static::findByStoreId($store_id);

            if (!$settings) {
                $settings = static::createForStore($store_id);
            }

            return $settings;
        }

        public function mergeDefaults()
        {
            return array_merge(static::defaultSettings()->toArray(), $this->toArray());
        }

    /*************
     * Collections
     */


}
