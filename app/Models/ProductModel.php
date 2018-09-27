<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

use App\Components\Money;
use App\Transformers\ProductModel\ProductModelFullTransformer;
use App\Transformers\ProductModel\ProductModelIncludedTransformer;
use App\Transformers\Serializers\SimpleArraySerializer;

use App\Models\KZ\KZPrice;

class ProductModel extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use Traits\CacheTrait;
    use Traits\DatetimeTrait;
    use Traits\VisibilityTrait;
    use Traits\HashidTrait;

    // id
    // name
    // price
    // visibility
    // product_title
    // product_description
    // mockup_format
    // product_category_id
    // preview_file_id
    // image_file_id
    // created_at
    // updated_at

    const VISIBILITY_VISIBLE = 'visible';
    const VISIBILITY_HIDDEN = 'hidden';

    const INVENTORY_STATUS_IN_STOCK = 'in_stock';
    const INVENTORY_STATUS_OUT_OF_STOCK = 'out_of_stock';

    const CACHE_TIME_MODEL_PRICE = 5;

    protected $table = 'product_models';
    protected $casts = [

    ];

    // revisions
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = [
        'inventory_status'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function($model) {
            event(
                new \App\Events\ProductModel\ProductModelSavedEvent($model)
            );
        });
    }

    public function applyPriceModifier($price, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (
            $this->template
            && $this->template->priceModifiers
            && $user
            && $user->id
        ) {
            foreach($this->template->priceModifiers as $modifier) {
                if ($modifier->user_id == $user->id) {
                    $price = $modifier->modifyPrice($price);
                }
            }
        }

        return $price;
    }

    /************
     * Accessors
     */

        // KZ prices
            public function getPriceAttribute()
            {
                return $this->getKZModelPrice($this, 'F');
            }

            public function getBackPriceAttribute()
            {
                return $this->getKZModelPrice($this, 'B');
            }

            public function getBothSidesPriceAttribute($value)
            {
                return $this->getKZModelPrice($this, 'A');
            }

        // prices tax
            public function getPriceTaxAttribute()
            {
                return Money::getTax($this->price);
            }

            public function getBackPriceTaxAttribute()
            {
                return Money::getTax($this->back_price);
            }

            public function getBothSidesPriceTaxAttribute($value)
            {
                return Money::getTax($this->both_sides_price);
            }

        // taxed prices
            public function getPriceTaxedAttribute()
            {
                return Money::applyTax($this->price);
            }

            public function getBackPriceTaxedAttribute()
            {
                return Money::applyTax($this->back_price);
            }

            public function getBothSidesPriceTaxedAttribute($value)
            {
                return Money::applyTax($this->both_sides_price);
            }

    /************
     * Mutators
     */



    /*********
     * Scopes
     */


    /***********
     * Relations
     */

        public function variants()
        {
            return $this->hasMany(\App\Models\ProductVariant::class);
        }

        public function catalogOptions()
        {
            return $this->belongsToMany(\App\Models\CatalogAttributeOption::class, 'product_model_option_relations', 'product_model_id', 'option_id');
        }

        public function optionsOfAttribute($name)
        {
            return $this->belongsToMany(\App\Models\CatalogAttributeOption::class, 'product_model_option_relations', 'product_model_id', 'option_id')
                ->whereHas('catalogAttribute', function ($query) use($name) {
                    $query->where('value', $name);
                });
        }

        public function template()
        {
            return $this->hasOne(\App\Models\ProductModelTemplate::class, 'id', 'template_id');
        }

        public function catalogAttributes()
        {
            return !empty($this->template) ? $this->template->catalogAttributes() : [];
        }

    /***********
     * Checks
     */

        public static function existsForTemplateWithOptionIds($template_id, $optionIds)
        {
            return (bool)static::findForTemplateWithOptionIds($template_id, $optionIds)->count();
        }

        public function isOutOfStock()
        {
            return $this->inventory_status == static::INVENTORY_STATUS_OUT_OF_STOCK;
        }

    /**********
     * Counters
     */



    /*************
     * Decorators
     */

        // prices
            public function frontPrice($user = null)
            {
                $price = $this->applyPriceModifier($this->price, $user);
                $price = Money::applyTax($price);
                return Money::USD($price);
            }

            public function backPrice($user = null)
            {
                $price = $this->applyPriceModifier($this->back_price, $user);
                $price = Money::applyTax($price);
                return Money::USD($price);
            }

            public function bothSidesPrice($user = null)
            {
                $price = $this->applyPriceModifier($this->both_sides_price, $user);
                $price = Money::applyTax($price);
                return Money::USD($price);
            }

        // taxes
            public function frontPriceTax()
            {
                return Money::USD($this->price_tax);
            }

            public function backPriceTax()
            {
                return Money::USD($this->back_price_tax);
            }

            public function bothSidesPriceTax()
            {
                return Money::USD($this->both_sides_price_tax);
            }

        // modifiers
            public function frontPriceModifier($user = null)
            {
                $modifiedPrice = $this->applyPriceModifier($this->price, $user);
                $difference = $this->price - $modifiedPrice;
                return Money::USD($difference);
            }

            public function backPriceModifier($user = null)
            {
                $modifiedPrice = $this->applyPriceModifier($this->back_price, $user);
                $difference = $this->back_price - $modifiedPrice;
                return Money::USD($difference);
            }

            public function bothSidesPriceModifier($user = null)
            {
                $modifiedPrice = $this->applyPriceModifier($this->both_sides_price, $user);
                $difference = $this->both_sides_price - $modifiedPrice;
                return Money::USD($difference);
            }

        public function getColorOption()
        {
            $color = $this->catalogOptions->first(function($option, $key) {
                return $option->catalogAttribute->value == \App\Models\CatalogAttribute::ATTRIBUTE_COLOR;
            });

            return $color;
        }

        public function getSizeOption()
        {
            $color = $this->catalogOptions->first(function($option, $key) {
                return $option->catalogAttribute->value == \App\Models\CatalogAttribute::ATTRIBUTE_SIZE;
            });

            return $color;
        }

        public function getVariantOption()
        {
            $color = $this->catalogOptions->first(function($option, $key) {
                return $option->catalogAttribute->value == \App\Models\CatalogAttribute::ATTRIBUTE_VARIANT;
            });

            return $color;
        }

    /*********
     * Helpers
     */


        public static function inventoryStatus($status)
        {
            $statuses = static::listInventoryStatuses();
            return $statuses[$status];
        }

        public static function listInventoryStatuses()
        {
            return collect([
                static::INVENTORY_STATUS_IN_STOCK => trans('labels.in_stock'),
                static::INVENTORY_STATUS_OUT_OF_STOCK => trans('labels.out_of_stock')
            ]);
        }

        protected function getKZModelPrice(ProductModel $model, $side = 'F')
        {
            $cacheValueNamesMap = [
                'F' => 'kz_model_price',
                'B' => 'kz_model_price_back_side',
                'A' => 'kz_model_price_both_sides',
            ];

            $cacheValueName = $cacheValueNamesMap[$side];

            if (!$this->hasCache($cacheValueName)) {
                $price = KZPrice::getProductModelPrice($model, $side);

                $this->addToCache($cacheValueName, $price, static::CACHE_TIME_MODEL_PRICE);
            }

            return $this->getCache($cacheValueName);
        }

    /**
     * Transformers
     */

        public function transformFull()
        {
            $resource = \FractalManager::item($this, new ProductModelFullTransformer);
            return \FractalManager::i(new SimpleArraySerializer())->createData($resource)->toArray();
        }

        public function transformIncluded()
        {
            $resource = \FractalManager::item($this, new ProductModelIncludedTransformer);
            return \FractalManager::i(new SimpleArraySerializer())->createData($resource)->toArray();
        }

    /***********
     * Functions
     */

        public function outOfStock()
        {
            if ($this->inventory_status != static::INVENTORY_STATUS_OUT_OF_STOCK) {
                $this->setInventoryStatus(static::INVENTORY_STATUS_OUT_OF_STOCK);
                return true;
            }

            return false;
        }

        public function inStock()
        {
            if ($this->inventory_status != static::INVENTORY_STATUS_IN_STOCK) {
                $this->setInventoryStatus(static::INVENTORY_STATUS_IN_STOCK);
                return true;
            }

            return false;
        }

        public function setInventoryStatus($status)
        {
            $this->inventory_status = $status;
            $this->save();
        }

        /**
         * We need to save price to show incomplete products later
         */
        public function savePrices()
        {
            $this->refreshCache = true;

            $this->price = $this->getPriceAttribute(null);
            $this->price_back = $this->getBackPriceAttribute(null);
            $this->price_both = $this->getBothSidesPriceAttribute(null);
            $this->save();

            $this->refreshCache = false;
        }

        protected static function manageInventory($inventoryStatus, $models)
        {
            $changedModels = [];

            foreach($models as $model) {

                if ($inventoryStatus == static::INVENTORY_STATUS_OUT_OF_STOCK) {
                    $changed = $model->outOfStock();
                }
                else {
                    $changed = $model->inStock();
                }

                if ($changed) {
                    $changedModels[] = $model;
                }
            }

            event(new \App\Events\ProductModel\ProductModelInventoryStatusChangedEvent(
                $inventoryStatus,
                $changedModels
            ));
        }

        public static function manageInventoryOutOfStock($models)
        {
            static::manageInventory(
                static::INVENTORY_STATUS_OUT_OF_STOCK,
                $models
            );
        }

        public static function manageInventoryInStock($models)
        {
            static::manageInventory(
                static::INVENTORY_STATUS_IN_STOCK,
                $models
            );
        }

    /*************
     * Collections
     */

        /**
         * Get by product sku and kz_option_id
         */
        public static function getForInventoryNotification($sku, $kz_option_ids)
        {
            $options = CatalogAttributeOption::getTableName();
            $template = ProductModelTemplate::getTableName();
            $model = static::getTableName();

            $mainQuery = static::select($model.'.*');

            foreach($kz_option_ids as $kz_option_id) {
                $mainQuery->whereIn('id', function($subquery) use($template, $model, $options, $sku, $kz_option_id) {
                    $subquery
                        ->select($model.'.id')
                        ->from($model)
                        ->join($template, $template.'.id', '=', $model.'.template_id')
                        ->join(
                            'product_model_option_relations',
                            'product_model_option_relations.product_model_id',
                            '=', $model.'.id'
                        )
                        ->join($options, $options.'.id', '=', 'product_model_option_relations.option_id')
                        ->where($template.'.sku', $sku)
                        ->where($options.'.id', $kz_option_id);
                });
            }

            return $mainQuery->get();
        }


        public static function findForTemplateWithOptionIds($template_id, $optionIds)
        {
            $options = CatalogAttributeOption::getTableName();
            $model = static::getTableName();

            $query = static::where('template_id', $template_id);

            if ($optionIds) {
                foreach ($optionIds as $optionId) {
                    $query->join(
                        'product_model_option_relations as relation_'.$optionId,
                        'relation_'.$optionId.'.product_model_id',
                        '=', $model.'.id'
                    )
                    ->where('relation_'.$optionId.'.option_id', $optionId);
                }
            }

            return $query;
        }
}
