<?php

namespace App\Models;

use FractalManager;
use Illuminate\Database\Eloquent\Model;
use App\Components\Money;

use App\Transformers\Product\ProductModelTemplateBriefTransformer;
use App\Transformers\Product\ProductModelTemplateFullTransformer;
use App\Transformers\Product\ProductModelTemplateIncludedTransformer;
use App\Transformers\Product\ProductModelTemplateKZTransformer;

class ProductModelTemplate extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use Traits\DatetimeTrait;
    use Traits\VisibilityTrait;
    //use Traits\HashidTrait;

    // id
    // name
    // price
    // visibility
    // product_title
    // product_description
    // category_id
    // preview_file_id
    // example_file_id
    // image_file_id
    // image_back_file_id
    // created_at
    // updated_at

    const VISIBILITY_VISIBLE = 'visible';
    const VISIBILITY_HIDDEN = 'hidden';

    protected $table = 'product_model_templates';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function($model) {
            \Event::fire(
                new \App\Events\ProductModelTemplate\ProductModelTemplateSavedEvent($model)
            );
        });
    }

    /************
     * Accessors
     */

    /************
     * Mutators
     */



    /*********
     * Scopes
     */

        public function scopeComplete($query)
        {
            $garment = Garment::getTableName();
            $model = ProductModel::getTableName();

            return $query
                // source templates
                ->where(function($query) use($garment) {
                    $query
                        ->where(function($query) use($garment) {
                            $query
                                ->whereHas('garment', function($query) use($garment) {
                                    $query
                                        ->where($garment.'.slug', Garment::SLUG_ALL_OVER_PRINT);
                                })
                                ->whereNotNull('example_file_id')
                                ->where('example_file_id', '!=', '');
                        })
                        ->orWhereHas('garment', function($query) use($garment) {
                            $query
                                ->where($garment.'.slug', '!=', Garment::SLUG_ALL_OVER_PRINT);
                        });
                })

                // images
                //->where(function($query) {
                //    $query
                //        ->whereNotNull('image_file_id')
                //        ->where('image_file_id', '!=', '');
                //})

                // garments
                ->where(function($query) use($garment) {
                    $query
                        ->where(function($query) use($garment) {
                            $query
                                ->whereHas('garment', function($query) use($garment) {
                                    $query
                                        ->where($garment.'.slug', Garment::SLUG_ALL_OVER_PRINT);
                                })
                                ->where(function($query) {
                                    $query
                                        ->whereNotNull('overlay_file_id')
                                        ->where('overlay_file_id', '!=', '')
                                        ->whereNotNull('overlay_back_file_id')
                                        ->where('overlay_back_file_id', '!=', '');
                                });
                        })
                        ->orWhereHas('garment', function($query) use($garment) {
                            $query
                                ->where($garment.'.slug', '!=', Garment::SLUG_ALL_OVER_PRINT);
                        });
                })

                // prices
                ->whereHas('models', function ($query) use($model) {
                    $query
                        ->where(function($query) use($model) {
                            $query
                                ->whereNotNull($model.'.price')
                                ->where($model.'.price', '!=', 0);
                        })
                        ->orWhere(function($query) use($model) {
                            $query
                                ->whereNotNull($model.'.price_back')
                                ->where($model.'.price_back', '!=', 0);
                        })
                        ->orWhere(function($query) use($model) {
                            $query
                                ->whereNotNull($model.'.price_both')
                                ->where($model.'.price_both', '!=', 0);
                        });
                });
        }

        public function scopeIncompleteSourceTemplates($query)
        {
            $garment = Garment::getTableName();

            return $query
                ->where(function($query) use($garment) {
                    $query
                        ->where(function($query) {
                            $query
                                ->whereNull('example_file_id')
                                ->orWhere('example_file_id', '');
                        })
                        ->whereHas('garment', function($query) use($garment) {
                            $query
                                ->where($garment.'.slug', Garment::SLUG_ALL_OVER_PRINT);
                        });
                });
        }

        public function scopeIncompleteImages($query)
        {
            return $query
                ->where(function($query) {
                    $query
                        ->whereNull('image_file_id')
                        ->orWhere('image_file_id', '');
                });
        }

        public function scopeIncompleteOverlays($query)
        {
            $garment = Garment::getTableName();

            return $query
                ->whereHas('garment', function($query) use($garment) {
                    $query
                        ->where($garment.'.slug', Garment::SLUG_ALL_OVER_PRINT);
                })
                ->where(function($query) {
                    $query
                        ->whereNull('overlay_file_id')
                        ->orWhere('overlay_file_id', '')
                        ->orWhereNull('overlay_back_file_id')
                        ->orWhere('overlay_back_file_id', '');
                });
        }

        public function scopeIncompletePrices($query)
        {
            $model = ProductModel::getTableName();

            return $query
                ->whereHas('models', function ($query) use($model) {
                    $query
                        ->where(function($query) use($model) {
                            $query
                                ->whereNull($model.'.price')
                                ->orWhere($model.'.price', 0);
                        })
                        ->where(function($query) use($model) {
                            $query
                                ->whereNull($model.'.price_back')
                                ->orWhere($model.'.price_back', 0);
                        })
                        ->where(function($query) use($model) {
                            $query
                                ->whereNull($model.'.price_both')
                                ->orWhere($model.'.price_both', 0);
                        });
                });
        }

    /***********
     * Relations
     */

        public function category()
        {
            return $this->belongsTo(CatalogCategory::class, 'category_id');
        }

        public function models()
        {
            return $this->hasMany(ProductModel::class, 'template_id');
        }

        public function preview()
        {
            return $this->hasOne(File::class, 'id', 'preview_file_id');
        }

        public function image()
        {
            return $this->hasOne(File::class, 'id', 'image_file_id');
        }

        public function imageBack()
        {
            return $this->hasOne(File::class, 'id', 'image_back_file_id');
        }

        public function example()
        {
            return $this->hasOne(FileAttachment::class, 'id', 'example_file_id');
        }

        public function overlay()
        {
            return $this->hasOne(File::class, 'id', 'overlay_file_id');
        }

        public function overlayBack()
        {
            return $this->hasOne(File::class, 'id', 'overlay_back_file_id');
        }

        public function garment()
        {
            return $this->belongsTo(Garment::class, 'garment_id');
        }

        public function shippingGroups()
        {
            return $this->belongsToMany(ShippingGroup::class, 'shipping_groups_templates', 'template_id', 'shipping_group_id');
        }

        public function priceModifiers()
        {
            return $this->hasMany(PriceModifier::class, 'template_id');
        }

    /***********
     * Checks
     */

        public function backPrintCanBeAddedOnItsOwn()
        {
            if ($this->category) {
                return (
                    $this->category->name != 'REG TEE'
                    && $this->category->name != 'Reg Tees'
                );
            }
            else {
                return false;
            }
        }

        public function isLASublimation()
        {
            return $this->garment->isAllOverPrintOrSimilar();
        }

        public function isIncompleteSourceTemplate()
        {
            return (bool)$this->example_file_id;
        }

        public static function skuIsBlackListed($sku)
        {
            $sku = strtolower($sku);
            return in_array($sku, [
                'g120',
                'aa2408',
                '71000',
                '71000l',
                '3633',
                '3600',
                '3001c',
                '2001'
            ]);
        }

        public function isPrepaid()
        {
            return (
                $this->category
                && $this->category->isPrepaid()
            );
        }

    /**********
     * Counters
     */

        public static function countWithoutSourceTemplates()
        {
            return static::incompleteSourceTemplates()->count();
        }

        public static function countWithoutImages()
        {
            return static::incompleteImages()->count();
        }

        public static function countWithoutOverlays()
        {
            return static::incompleteOverlays()->count();
        }

        public static function countWithoutPrices()
        {
            return static::incompletePrices()->count();
        }

        public static function countComplete()
        {
            return static::complete()->count();
        }

    /*************
     * Decorators
     */

        public function catalogAttributes()
        {
            return !empty($this->category) ? $this->category->catalogAttributes : [];
        }

        public function catalogAttribute($args){
            return CatalogAttribute::where('value',$args)->pluck("value", "name")->all();
        }

        public function catalogAttributesOptions()
        {
            return !empty($this->category) ? $this->category->catalogAttributes : [];
        }

        public function catalogAttributeOptions($args){
            return CatalogAttributeOption::where('attribute_id',$args)->pluck("value", "id")->all();
        }

        public function priceMoney()
        {
            return Money::USD($this->price);
        }

       

    /*********
     * Helpers
     */
    
    

    /**************
     * Transformers
     */

        public function transformBrief()
        {
            return FractalManager::serializeItem($this, new ProductModelTemplateBriefTransformer);
        }

        public function transformFull()
        {
            return FractalManager::serializeItem($this, new ProductModelTemplateKZTransformer);
        }

        public static function transformIncludedCollection($collection)
        {
            return FractalManager::serializeCollection($collection, new ProductModelTemplateIncludedTransformer);
        }

    /***********
     * Functions
     */



    /*************
     * Collections
     */

        public static function getAllVisible()
        {
            $q = static::visible();

            if (getenv('APP_ENV') != 'local') {
                $q->complete();
            }

            return $q->get();
        }

        public static function getAvailableForShippingGroups($shippingGroup)
        {
            return static::where(function($q) use($shippingGroup) {
                    $q
                        ->whereDoesntHave('shippingGroups')
                        ->orWhereHas('shippingGroups', function($query) use($shippingGroup) {
                            $query
                                ->where(ShippingGroup::getTableName().'.id', $shippingGroup->id);
                        });
                })
                ->get();
        }
}
