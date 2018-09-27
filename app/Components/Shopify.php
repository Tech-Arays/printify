<?php
namespace App\Components;

use DateTime;
use Exception;
use App;
//use App\Components\Logger;

class Shopify
{
    const WEBHOOK_TOPIC_ORDERS_CREATE = 'orders/create';
    const WEBHOOK_TOPIC_ORDERS_DELETE = 'orders/delete';
    const WEBHOOK_TOPIC_ORDERS_UPDATED = 'orders/updated';
    const WEBHOOK_TOPIC_ORDERS_PAID = 'orders/paid';
    const WEBHOOK_TOPIC_ORDERS_CANCELLED = 'orders/cancelled';
    const WEBHOOK_TOPIC_ORDERS_FULFILLED = 'orders/fulfilled';
    const WEBHOOK_TOPIC_ORDERS_PARTIALLY_FULFILLED = 'orders/partially_fulfilled';

    const WEBHOOK_TOPIC_ORDER_TRANSACTIONS_CREATE = 'order_transactions/create';

    const WEBHOOK_TOPIC_PRODUCT_CREATE = 'products/create';
    const WEBHOOK_TOPIC_PRODUCT_UPDATE = 'products/update';
    const WEBHOOK_TOPIC_PRODUCT_DELETE = 'products/delete';

    const WEBHOOK_TOPIC_APP_UNINSTALLED = 'app/uninstalled';

    const METAFIELDS_NAMESPACE_GLOBAL = 'global';
    const METAFIELDS_KEY_PRODUCT = 'mntz_product';
    const METAFIELDS_KEY_MODEL_ID = 'mntz_model_id';
    const METAFIELDS_KEY_PRODUCT_VARIANT_ID = 'mntz_product_variant_id';


    const ORDER_FINANCIAL_STATUS_REFUNDED = 'refunded';

    protected static $instance = null;
    public $api = null;
    protected $domain = null;
    protected $accessToken = null;

    public static function i($domain = null, $accessToken = null)
    {
        if (
            empty(static::$instance)
            || empty(static::$instance->api)
            || static::$instance->domain != $domain
            || static::$instance->accessToken != $accessToken
        ) {
            static::$instance = new static();

            static::$instance->setDomain($domain);
            static::$instance->setAccessToken($accessToken);
            static::$instance->initApi();
        }

        return static::$instance;
    }

    public static function getMyshopifyDomain($shopDomain)
    {
        if (!stristr($shopDomain, 'myshopify.com')) {
            $headers = get_headers('http://'.$shopDomain.'/admin/shop.json', true);

            if (
                !empty($headers)
                && !empty($headers['Location'])
            ) {
                $shopDomain = parse_url($headers['Location'], PHP_URL_HOST);
            }
        }

        return $shopDomain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function initApi()
    {
        $this->api = App::make('ShopifyAPI', [
            'API_KEY' => config('services.shopify.client_id'),
            'API_SECRET' => config('services.shopify.client_secret'),
            'SHOP_DOMAIN' => $this->domain,
            'ACCESS_TOKEN' => $this->accessToken
        ]);
    }

    public function getShop()
    {
        return $this->api->call([
            'URL' => 'shop.json',
            'METHOD' => 'GET'
        ]);
    }

    public function getAdminShopInfo()
    {
        return $this->api->call([
            'URL' => '/admin/shop.json',
            'METHOD' => 'GET'
        ]);
    }

    public function getProducts()
    {
        return $this->api->call([
            'URL' => 'admin/products.json',
            'METHOD' => 'GET'
        ]);
    }

    public function getProduct($id)
    {
        return $this->api->call([
            'URL' => 'admin/products/'.$id.'.json',
            'METHOD' => 'GET'
        ]);
    }

    public function pushProduct($data)
    {
        return $this->api->call([
            'URL' => 'admin/products.json',
            'METHOD' => 'POST',
            'DATA' => $data
        ]);
    }

    public function deleteProduct($id)
    {
        return $this->api->call([
            'URL' => 'admin/products/'.$id.'.json',
            'METHOD' => 'DELETE',
            'DATA' => []
        ]);
    }

    public function updateVariant($id, $variantData)
    {
        return $this->api->call([
            'URL' => 'admin/variants/'.$id.'.json',
            'METHOD' => 'PUT',
            'DATA' => [
                'variant' => array_merge($variantData, [
                    'id' => $id
                ])
            ]
        ]);
    }

    public function deleteVariant($productId, $variantId)
    {
        return $this->api->call([
            'URL' => 'admin/products/'.$productId.'/variants/'.$variantId.'.json',
            'METHOD' => 'DELETE',
            'DATA' => []
        ]);
    }

    public function addProductImages($id, $data)
    {
        return $this->api->call([
            'URL' => 'admin/products/'.$id.'/images.json',
            'METHOD' => 'POST',
            'DATA' => $data
        ]);
    }

    public function getProductMetafields($id)
    {
        return $this->api->call([
            'URL' => '/admin/products/'.$id.'/metafields.json',
            'METHOD' => 'GET'
        ]);
    }

    public function getProductVariantMetafields($id)
    {
        return $this->api->call([
            'URL' => '/admin/variants/'.$id.'/metafields.json',
            'METHOD' => 'GET'
        ]);
    }

    public function allWebhooksExist()
    {
        $webhooks = static::getWebhooksList();
        return $this->webhooksExist($webhooks);
    }

    public function getWebhooks()
    {
        return $this->api->call([
            'URL' => 'admin/webhooks.json',
            'METHOD' => 'GET'
        ]);
    }

    public static function getWebhooksList()
    {
        return array_merge(
            static::getOrdersWebhooksList(),
            static::getProductWebhooksList(),
            static::getAppWebhooksList()
        );
    }

    protected function webhookExists($topic)
    {
        $call = $this->getWebhooks();
        $exists = false;

        if (!empty($call->webhooks)) {
            foreach ($call->webhooks as $webhook) {
                if ($webhook->topic == $topic) {
                    $exists = true;
                }
            }
        }

        return $exists;
    }

    protected function webhooksExist($topics)
    {
        $call = $this->getWebhooks();

        $exist = [];

        if (!empty($call->webhooks)) {
            foreach ($call->webhooks as $webhook) {
                if (in_array($webhook->topic, $topics)) {
                    $exist[$webhook->topic] = 1;
                }
            }
        }

        return array_sum($exist) == count($topics);
    }

    protected function createWebhook($topic, $url)
    {
        // TODO: for testing
        if(env('APP_ENV') === 'local') {
            $url = str_replace(env('APP_HOME'), env('APP_NGROK_HOME'), $url);
            $url = str_replace(env('APP_PPM_HOME'), env('APP_NGROK_HOME'), $url);
        }

        return $this->api->call([
            'URL' => 'admin/webhooks.json',
            'METHOD' => 'POST',
            'DATA' => [
                'webhook' => [
                    'topic' => $topic,
                    'address' => 'https://39ecb8d4.ngrok.io/',
                    'format' => 'json'
                ]
            ]
        ]);
    }

    protected function createWebhookIfNotExists($topic, $url)
    {
        $webhookExists = $this->webhookExists($topic);
        if (!$webhookExists) {
            return $this->createWebhook($topic, $url);
        }
    }

    public static function getOrdersWebhooksList()
    {
        return [
            static::WEBHOOK_TOPIC_ORDERS_CREATE,
            static::WEBHOOK_TOPIC_ORDERS_DELETE,
            static::WEBHOOK_TOPIC_ORDERS_UPDATED,
            static::WEBHOOK_TOPIC_ORDERS_PAID,
            static::WEBHOOK_TOPIC_ORDERS_CANCELLED,
            static::WEBHOOK_TOPIC_ORDERS_FULFILLED,
            static::WEBHOOK_TOPIC_ORDERS_PARTIALLY_FULFILLED,
            static::WEBHOOK_TOPIC_ORDER_TRANSACTIONS_CREATE
        ];
    }

    protected function createOrdersWebhooksIfNotExist($url)
    {
        $topics = static::getOrdersWebhooksList();

        foreach ($topics as $topic) {
            $this->createWebhookIfNotExists($topic, $url);
        }
    }

    public static function getProductWebhooksList()
    {
        return [
            static::WEBHOOK_TOPIC_PRODUCT_CREATE,
            static::WEBHOOK_TOPIC_PRODUCT_UPDATE,
            static::WEBHOOK_TOPIC_PRODUCT_DELETE
        ];
    }

    protected function createProductsWebhooksIfNotExist($url)
    {
        $topics = static::getProductWebhooksList();

        foreach ($topics as $topic) {
            $this->createWebhookIfNotExists($topic, $url);
        }
    }

    public static function getAppWebhooksList()
    {
        return [
            static::WEBHOOK_TOPIC_APP_UNINSTALLED
        ];
    }

    protected function createAppWebhooksIfNotExist($url)
    {
        $topics = static::getAppWebhooksList();

        foreach ($topics as $topic) {
            $this->createWebhookIfNotExists($topic, $url);
        }
    }

    public function createWebhooks()
    {
        $this->createOrdersWebhooksIfNotExist(
            url('/dashboard/orders/webhook')
        );

        $this->createProductsWebhooksIfNotExist(
            url('/dashboard/products/webhook')
        );

        $this->createAppWebhooksIfNotExist(
            url('/dashboard/store/webhook')
        );
    }

    public function replaceWebhooks()
    {
        $this->removeAllWebhooks();
        $this->createWebhooks();
    }

    public function removeAllWebhooks()
    {
        $call = $this->getWebhooks();

        if (empty($call->webhooks)) {
            return;
        }

        foreach ($call->webhooks as $webhook) {
            $this->api->call([
                'URL' => 'admin/webhooks/'.$webhook->id.'.json',
                'METHOD' => 'DELETE'
            ]);
        }
    }

    public function getOrder($id)
    {
        return $this->api->call([
            'URL' => 'admin/orders/'.$id.'.json',
            'METHOD' => 'GET'
        ]);
    }

    public function getOrders()
    {
        return $this->api->call([
            'URL' => 'admin/orders.json?status=any&limit=250',
            'METHOD' => 'GET'
        ]);
    }

    public function searchOrders($search)
    {
        return $this->api->call([
            'URL' => 'admin/orders.json?name='.$search.'&status=any&limit=250',
            'METHOD' => 'GET'
        ]);
    }

    public function searchOrdersAfter(DateTime $datetime)
    {
        return $this->api->call([
            'URL' => 'admin/orders.json?created_at_min='.$datetime->format(DateTime::W3C).'&status=any&limit=250',
            'METHOD' => 'GET'
        ]);
    }


    public function fulfillOrder($id, $data)
    {
        try {
            $result = $this->api->call([
                'URL' => 'admin/orders/'.$id.'/fulfillments.json',
                'METHOD' => 'POST',
                'DATA' => [
                    'fulfillment' => $data
                ]
            ]);
        } catch(Exception $e) {
           /* if (static::is404Exception($e)) {
                Logger::i(Logger::API_KZ_ORDER_FULFILLMENTS)
                    ->error('fulfillOrder shopify error', [
                        'id' => $id,
                        'data' => $data,
                        'message' => $e->getMessage()
                    ]);
            }
            else {
                throw $e;
            }*/
        }
    }

    public static function verifyWebhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, getenv('SHOPIFY_CLIENT_SECRET'), true));
        return ($hmac_header == $calculated_hmac);
    }

    public static function is404Exception($exception)
    {
        return stristr($exception->getMessage(), '404 Not Found');
    }

    public static function is402Exception($exception)
    {
        return stristr($exception->getMessage(), '402 Payment Required');
    }
}
