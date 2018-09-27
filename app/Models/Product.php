<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Transformers\Product\ProductFullTransformer;
use App\Transformers\Product\ProductEditingTransformer;
use App\Transformers\Serializers\SimpleArraySerializer;

class Product extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Culpa\Traits\Blameable;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use Traits\MetableTrait;
    use Traits\DatetimeTrait;
    use Traits\ModerationStatusTrait;
    use Traits\HashidTrait;

    // id
    // user_id
    // name
    // status
    // type
    // created_at
    // updated_at

    const STATUS_DRAFT = 'draft';
    const STATUS_QUEUED_FOR_SYNC = 'queued_for_sync';
    const STATUS_ACTIVE = 'active';
    const STATUS_IGNORED = 'ignored';

    const MODERATION_STATUS_NOT_APPROVED = 'not_approved'; // waiting for the client
    const MODERATION_STATUS_ON_MODERATION = 'on_moderation'; // waiting for the admin
    const MODERATION_STATUS_APPROVED = 'approved';
    const MODERATION_STATUS_AUTO_APPROVED = 'auto_approved';
    const MODERATION_STATUS_DECLINED = 'declined';

    const TYPE_LOCAL = 'local';
    const TYPE_VENDOR = 'vendor';

    const META_STITCHES = 'stitches';
    const META_THREAD_COLORS = 'thread_colors';

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'store_id',
        'provider_product_id'
    ];

    protected $casts = [
        'meta' => 'object',
        'canvas_meta' => 'object'
    ];

    // revisions
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = [
        'status',
        'moderation_status',
        'moderation_status_comment'
    ];

    // blameable
    protected $blameable = [
        'created' => 'user_id'
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array_merge($this->attributes, [
          'status' => static::STATUS_DRAFT,
          'moderation_status' => static::MODERATION_STATUS_NOT_APPROVED,
        ]), true);
        parent::__construct($attributes);
    }

    /************
     * Mutators
     */

        public function setStatusAttribute($value)
        {
            if (!$this->status && !$value) {
                $this->attributes['status'] = static::STATUS_DRAFT;
            }
            else {
                $this->attributes['status'] = $value;
            }
        }

    /*********
     * Scopes
     */

        public function scopeOwns($query, $user)
        {
            return $query
                ->where('user_id', $user->id);
        }


    /***********
     * Relations
     */

        public function user()
        {
            return $this->belongsTo(\App\Models\User::class, 'user_id');
        }

        public function store()
        {
            return $this->belongsTo(\App\Models\Store::class, 'store_id');
        }

        public function variantsSynced()
        {
            return $this->hasMany(\App\Models\ProductVariant::class)
                ->whereNotNull('product_model_id');
        }

        public function variantsNotSynced()
        {
            return $this->hasMany(\App\Models\ProductVariant::class)
                ->whereNull('product_model_id');
        }

        public function variantsIgnored()
        {
            return $this->hasMany(\App\Models\ProductVariant::class)
                ->where('status', ProductVariant::STATUS_IGNORED);
        }

        public function variants()
        {
            return $this->hasMany(\App\Models\ProductVariant::class);
        }

        public function filePreview()
        {
            $clientFile = $this->clientFiles()
                ->first();
            if ($clientFile) {
                return $clientFile->printFile;
            }

            return null;
        }

        public function mockupPreview()
        {
            $clientFile = $this->clientFiles()
                ->first();

            if ($clientFile) {
                return $clientFile->mockup;
            }

            return null;
        }

        public function mockupPreviewBack()
        {
            $clientFile = $this->clientFiles()
                ->where('design_location', ProductClientFile::LOCATION_BACK)
                ->first();
            if ($clientFile) {
                return $clientFile->mockup;
            }

            return null;
        }

        public function filePreviewBack()
        {
            $clientFile = $this->clientFiles()
                ->where('design_location', ProductClientFile::LOCATION_BACK)
                ->first();
            if ($clientFile) {
                return $clientFile->printFile;
            }

            return null;
        }

        public function clientFiles()
        {
            return $this->hasMany(\App\Models\ProductClientFile::class);
        }

        public function firstClientFile()
        {
            return $this->clientFiles ? $this->clientFiles->first() : null;
        }

        public function productModelTemplate()
        {
            return $this->variants ? $this->variants->first()->model->template : null;
        }

        public function template()
        {
            return $this->productModelTemplate();
        }

        public function payments()
        {
            return $this->hasMany(Payment::class);
        }

        public function firstSuccessfulPayment()
        {
            return $this->hasMany(Payment::class)
                ->where('status', Payment::STATUS_SUCCEEDED)
                ->first();
        }

    /***********
     * Checks
     */

        public function isDraft()
        {
            return $this->status == static::STATUS_DRAFT;
        }

        public function isQueuedForSync()
        {
            return $this->status == static::STATUS_QUEUED_FOR_SYNC;
        }

        public function isActive()
        {
            return $this->status == static::STATUS_ACTIVE;
        }

        public function isIgnored()
        {
            return $this->status == static::STATUS_IGNORED;
        }

        public function isSynced()
        {
            return (bool)$this->provider_product_id;
        }

        public function isPrepaid()
        {
            return (
                $this->template()
                && $this->template()->category
                && $this->template()->category->isPrepaid()
            );
        }

    /**********
     * Counters
     */



    /*************
     * Decorators
     */

        public function getStatusName()
        {
            return static::statusName($this->status);
        }

        public function getTypeName()
        {
            return static::typeName($this->type);
        }

        public function providerProductEditUrl()
        {
            if ($this->store && $this->store->domain && $this->provider_product_id) {
                return url('https://'.$this->store->domain.'/admin/products/'.$this->provider_product_id);
            }
            else {
                return null;
            }
        }

        public function providerImage()
        {
            if (isset($this->meta->image)) {
                return array_get((array)$this->meta->image, 'src');
            }
            else {
                return null;
            }
        }

        public function getVariantAttributeOptions()
        {
            $variants = $this->variants;

            $attrs = collect([]);
            $uniqueOptions = collect([]);
            foreach($variants as $variant) {
                if ($variant->model) {
                    foreach($variant->model->catalogOptions as $option) {
                        $attr = $option->catalogAttribute;
                        if (!isset($attrs[$attr->id])) {
                            $attrs[$attr->id] = $attr;
                            $attrs[$attr->id]->selectedOptions = collect([]);
                        }

                        if($uniqueOptions->search($option->value) === false) {
                            $attrs[$attr->id]->selectedOptions->push($option);
                            $uniqueOptions->push($option->value);
                        }
                    }
                }
            }
            return $attrs;
        }

    /*********
     * Helpers
     */

        public static function statusName($status)
        {
            $statuses = static::listStatuses();
            return isset($statuses[$status]) ? $statuses[$status] : null;
        }

        public static function listStatuses()
        {
            return [
                static::STATUS_DRAFT => trans('labels.draft'),
                static::STATUS_QUEUED_FOR_SYNC => trans('labels.queued_for_sync'),
                static::STATUS_ACTIVE => trans('labels.active'),
                static::STATUS_IGNORED => trans('labels.ignored')
            ];
        }

        public static function typeName($type)
        {
            $types = static::listTypes();
            return isset($types[$type]) ? $types[$type] : null;
        }

        public static function listTypes()
        {
            return [
                static::TYPE_LOCAL => trans('labels.type__local'),
                static::TYPE_VENDOR => trans('labels.type__vendor')
            ];
        }

    /**
     * Transformers
     */

        public function transformFull()
        {
            return \FractalManager::serializeItem($this, new ProductFullTransformer);
        }

        public function transformForEditing()
        {
            return \FractalManager::serializeItem($this, new ProductEditingTransformer);
        }


    /***********
     * Functions
     */

        public function createProduct()
        {
            $result = $this->save();

            return $result;
        }

        public static function updateShopifyProductIfExists(User $user, Store $store, $shopifyProduct)
        {
            $product = static::where([
                'user_id' => $user->id,
                'store_id' => $store->id,
                'provider_product_id' => $shopifyProduct->id
            ])->first();

            if ($product) {
                return static::createOrUpdateShopifyProduct($user, $store, $shopifyProduct);
            }
            else {
                return $product;
            }
        }

        public static function createOrUpdateShopifyProduct(User $user, Store $store, $shopifyProduct)
        {
            $product = static::firstOrNew([
                'user_id' => $user->id,
                'store_id' => $store->id,
                'provider_product_id' => $shopifyProduct->id
            ]);

            $product->name = $shopifyProduct->title;
            $product->type = static::TYPE_VENDOR;
            $product->provider_product_id = $shopifyProduct->id;
            $product->meta = $shopifyProduct;

            if (!$product->id) {
                $product->createProduct();
            }
            else {
                $product->save();
            }

            foreach ($shopifyProduct->variants as $variant) {
                ProductVariant::createOrUpdateShopifyVariant($user, $product, $variant);
            }

            return $product;
        }

        public function updateShopifyProduct($shopifyProduct)
        {
            $this->provider_product_id = $shopifyProduct->id;
            $this->meta = $shopifyProduct;
            $this->save();

            foreach ($shopifyProduct->variants as $variant) {
                ProductVariant::createOrUpdateShopifyVariant($this->user, $this, $variant);
            }

            return $this;
        }

        public function changeStatus($status)
        {
            $this->status = $status;
            return $this->save();
        }

        public function activate()
        {
            return $this->changeStatus(static::STATUS_ACTIVE);
        }

        public function ignore()
        {
            return $this->changeStatus(static::STATUS_IGNORED);
        }

        public function queuedForSync()
        {
            return $this->changeStatus(static::STATUS_QUEUED_FOR_SYNC);
        }

        public static function findByProviderId($id, $store_id)
        {
            return static::where('provider_product_id', $id)
                ->where('store_id', $store_id)
                ->first();
        }

    /*************
     * Collections
     */

}
