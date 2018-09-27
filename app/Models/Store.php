<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Exception;
use Log;
use Bugsnag;

use App\Components\Shopify;

class Store extends Model
{   
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Culpa\Traits\Blameable;
    use Traits\DatetimeTrait;
    use Traits\HashidTrait;
    use Traits\CacheTrait;
    const CONNECT_MODE__UNIQUE_REPLACE = 'unique_replace'; // store with the same provider_store_id will be replaced
    const CONNECT_MODE__MULTIPLE = 'multiple'; // few stores with the same domain could exists parallel

    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    const PROVIDER_SHOPIFY = 'shopify';

    protected $table = 'stores';

    protected $fillable = [
       
    ];

    protected $casts = [

    ];

    // blameable
    protected $blameable = [
        'created' => 'user_id'
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array_merge($this->attributes, [
          'status' => static::STATUS_ACTIVE,
        ]), true);
        parent::__construct($attributes);
    }

    public static function saveTemporaryShopifyStore($shop, $accessToken){
        $store = new static();
        $store->prepareShopifyStore($shop->domain, $shop->id, $accessToken);
        $store->name = $shop->name;
        session(['preparedStore' => $store]);
    }

    public function prepareShopifyStore($shopDomain, $shopId, $accessToken){
        $this->name = $shopDomain;
        $this->provider = static::PROVIDER_SHOPIFY;
        $this->provider_store_id = $shopId;
        $this->domain = $shopDomain;

        // get real shopify domain
        $this->provider_domain = Shopify::getMyshopifyDomain($shopDomain);

        $this->access_token = $accessToken;
        $this->website = 'https://'.$shopDomain;
    }
    
    public static function shopExistsForCurrentUser($shopDomain)
    {
        return static::hasDomain($shopDomain)
            ->owns(auth()->user())
            ->first();
    }

    public static function getStoresByDomainExceptCurrentUser($shopDomain)
    {
        $user_id = auth()->user() ? auth()->user()->id : 0;
        return static::hasDomain($shopDomain)
            ->where('user_id', '!=', $user_id)
            ->get();
    }

    public static function findByDomainForCurrentUser($domain)
    {
        return static::hasDomain($domain)
            ->owns(auth()->user())
            ->first();
    }

    /*********
     * Scopes
     */

    public function scopeOwns($query, $user)
    {
        return $query
            ->where('user_id', ($user ? $user->id : 0));
    }

    public function scopeHasDomain($query, $domain)
    {
        return $query
            ->where(function($q) use($domain) { $q
                ->where('domain', $domain)
                ->orWhere('provider_domain', $domain);
            });
    }

    public function scopeWhichSynced($query)
    {
        return $query->where('access_token', '!=', null);
    }

    public function createStore($name = '')
    {
        if ($name) {
            $this->name = $name;
        }
        $result = $this->save();

        \Event::fire(new \App\Events\Store\StoreCreatedEvent($this));

        //StoreSettings::createForStoreIfNotExists($this->id);

        return $result;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shopifyDomain()
    {
        return $this->provider_domain ?: $this->domain;
    }

    public function shopifyWebhooksAreSetUp($forceUpdate = false)
    {
        if (!$this->hasCache('webhooks_exist') || $forceUpdate) {

            $exist = false;
            try {
                $exist = Shopify::i($this->shopifyDomain(), $this->access_token)
                    ->allWebhooksExist();
            }
            catch(Exception $e) {
                Log::error($e);

                $logMetadata = [
                    'store' => $this->toArray()
                ];
                Log::error('Cannot check webhooks for the store', $logMetadata);
                Bugsnag::registerCallback(function ($report) use($logMetadata) {
                    $report->setMetaData($logMetadata);
                });
                Bugsnag::notifyException($e);
            }

            $this->putToCache('webhooks_exist', $exist, 60 * 24 * 30);
        }

        $exist = $this->getCache('webhooks_exist');

        if ($exist) {
            $this->getWebhooks($forceUpdate);
        }

        return $exist;
    }
    public function getWebhooks($forceUpdate = false)
    {
        if (!$this->hasCache('webhooks') || $forceUpdate) {

            $webhooks = [];
            try {
                $webhooks = Shopify::i($this->shopifyDomain(), $this->access_token)
                    ->getWebhooks();
            }
            catch(Exception $e) {
                Log::error($e);

                $logMetadata = [
                    'store' => $this->toArray()
                ];
                Log::error('Cannot get webhooks for the store', $logMetadata);
                Bugsnag::registerCallback(function ($report) use($logMetadata) {
                    $report->setMetaData($logMetadata);
                });
                Bugsnag::notifyException($e);
            }

            $this->putToCache('webhooks', $webhooks, 60 * 24 * 30);
        }

        return $this->getCache('webhooks');
    }  

    public function isInSync()
    {
        return (bool)($this->access_token);
    } 
    public function vendorProductsSynced()
    {
        return $this->hasMany(Product::class)
            ->where('type', Product::TYPE_VENDOR)
            ->whereIn('status', [
                Product::STATUS_ACTIVE,
                Product::STATUS_IGNORED
            ]);
    }

    public function vendorProductsPending()
    {
        return $this->hasMany(Product::class)
            ->where('type', Product::TYPE_VENDOR)
            ->whereIn('status', [
                Product::STATUS_DRAFT,
                Product::STATUS_QUEUED_FOR_SYNC
            ]);
    }

    public function vendorProductsApproved()
    {
        return $this->hasMany(Product::class)
            ->where('type', Product::TYPE_VENDOR)
            ->where('moderation_status', Product::MODERATION_STATUS_APPROVED);
    }

    public function vendorProductsActive()
    {
        return $this->hasMany(Product::class)
            ->where('type', Product::TYPE_VENDOR)
            ->where('status', Product::STATUS_ACTIVE)
            ->where('moderation_status', Product::MODERATION_STATUS_APPROVED);
    }

    public function vendorProductsAllowedDirectOrder()
    {
        return $this->hasMany(Product::class)
            ->where('type', Product::TYPE_VENDOR)
            ->whereIn('moderation_status', [
                Product::MODERATION_STATUS_APPROVED,
                Product::MODERATION_STATUS_AUTO_APPROVED
            ]);
    }
    
    public static function findStoreWithRelations($store_id)
    {
        return static::with('vendorProductsSynced.variants.model')
            ->with('vendorProductsSynced.variantsSynced.model')
            ->with('vendorProductsSynced.variantsNotSynced.model')
            ->with('vendorProductsSynced.variantsIgnored.model')
            ->with('vendorProductsPending.variants.model')
            ->with('vendorProductsPending.variantsSynced.model')
            ->with('vendorProductsPending.variantsNotSynced.model')
            ->with('vendorProductsPending.variantsIgnored.model')
            ->find($store_id);
    }
}
