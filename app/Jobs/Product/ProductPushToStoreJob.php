<?php

namespace App\Jobs\Product;

use DB;
use Log;
use Exception;
use Bugsnag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Job;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Components\Shopify;

class ProductPushToStoreJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->product;

        $store = $product->store;
        if (!$store || !$store->isInSync()) {
            Log::error('Push to store exception: '.trans('messages.you_cannot_push_product_to_the_selected_store'));
            $e = new Exception(trans('messages.you_cannot_push_product_to_the_selected_store'));
            Bugsnag::notifyException($e);
            throw $e;
        }

        // push products
        try {
            $pushProductCall = Shopify::i($store->shopifyDomain(), $store->access_token)
                ->pushProduct([
                    'product' => $product->meta
                ]);
        }
        catch(Exception $e) {
            $logMetadata = [
                'product' => $product,
                '$product->meta' => $product->meta
            ];
            Log::error('Push to store exception: '.$e->getMessage().' Stack trace: '.$e->getTraceAsString(), [
                'logMetadata' => $logMetadata
            ]);
            Bugsnag::registerCallback(function ($report) use($logMetadata) {
                $report->setMetaData($logMetadata);
            });
            Bugsnag::notifyException($e);

            if (stristr($e->getMessage(), '429 Too Many Requests')) {
                sleep(30);
            }

            throw $e;
        }

        $shopifyVariantIds = [];
        foreach($pushProductCall->product->variants as $variant) {
            $shopifyVariantIds[] = $variant->id;
        }

        DB::beginTransaction();

        $call = null;
        try {
        // get product
            $call = Shopify::i($store->shopifyDomain(), $store->access_token)
                ->getProduct($pushProductCall->product->id);

        // update local product and variants
            $product->provider_product_id = $call->product->id;
            $product->meta = $call->product;
            $product->save();

            foreach ($call->product->variants as $shopifyVariant) {
                $variant = ProductVariant::findForProductByShopifyOptions($product, $shopifyVariant);
                if ($variant) {
                    $variant->provider_variant_id = $shopifyVariant->id;
                    $variant->meta = $shopifyVariant;
                    $variant->save();
                }
            }

            // push image
            $this->pushImage(
                $store,
                $product,
                $pushProductCall->product->id,
                $shopifyVariantIds
            );

            $product->activate();

        }
        catch(Exception $e) {
            DB::rollback();

            $logMetadata = [
                'product' => $product,
                '$call' => $call
            ];
            Log::error('Push images to store exception: '.$e->getMessage().' Stack trace: '.$e->getTraceAsString(), [
                'logMetadata' => $logMetadata
            ]);
            Bugsnag::registerCallback(function ($report) use($logMetadata) {
                $report->setMetaData($logMetadata);
            });
            Bugsnag::notifyException($e);

            // reset status
            $product->queuedForSync();

            // rollback/delete from shopify if it already synced
            if ($product->isSynced()) {
                try {
                    Shopify::i($store->shopifyDomain(), $store->access_token)
                        ->deleteProduct($product->provider_product_id);
                }
                catch(Exception $e) {
                    if (Shopify::is404Exception($e)) {
                        // product doesn't exist on shopify, do nothing
                    }
                    else {
                        Log::error($e);
                        Bugsnag::notifyException($e);
                        throw $e;
                    }
                }
            }

            if (stristr($e->getMessage(), '429 Too Many Requests')) {
                sleep(30);
            }

            throw $e;
        }

        DB::commit();
    }

    private function pushImage($store, $product, $shopifyProductId, $shopifyVariantIds)
    {
        $allVariantsProviderIds = [];
        $colorVariantsProviderIds = [];
        $colorPath = [];
        $colorPathBack = [];

        // we will push images only for unique colors
        foreach($product->variants as $variant) {
            $allVariantsProviderIds[] = $variant->provider_variant_id;

            // some variants will have only sizes
            if (!$variant->model || !$variant->model->getColorOption()) {
                continue;
            }

            if (!isset($colorVariantsProviderIds[$variant->model->getColorOption()->id])) {
                $colorVariantsProviderIds[$variant->model->getColorOption()->id] = [];
                $colorPath[$variant->model->getColorOption()->id] = [];
            }

            $colorVariantsProviderIds[$variant->model->getColorOption()->id][] = $variant->provider_variant_id;

            if (
                ($variant->mockups && $variant->mockups->first())
                && $variant->mockupsBack && $variant->mockupsBack->first()
            ) {
                $colorPath[$variant->model->getColorOption()->id] = $variant->mockups->first()->file->path();
                $colorPathBack[$variant->model->getColorOption()->id] = $variant->mockupsBack->first()->file->path();
            }
            else if ( $variant->mockups && $variant->mockups->first() ) {
                $colorPath[$variant->model->getColorOption()->id] = $variant->mockups->first()->file->path();
            }
            else if ( $variant->mockupsBack && $variant->mockupsBack->first() ) {
                $colorPath[$variant->model->getColorOption()->id] = $variant->mockupsBack->first()->file->path();
            }
        }

        if (!empty($colorVariantsProviderIds)) {
            foreach($colorVariantsProviderIds as $colorId => $provider_variant_ids) {
                $imageData = [
                    'image' => [
                        'variant_ids' => $provider_variant_ids,
                        'attachment' => base64_encode(
                            file_get_contents($colorPath[$colorId])
                        )
                    ]
                ];
                Shopify::i($store->shopifyDomain(), $store->access_token)
                    ->addProductImages($shopifyProductId, $imageData);
                unset($imageData);

            // back images
                if (!empty($colorPathBack)) {
                    $imageData = [
                        'image' => [
                            'attachment' => base64_encode(
                                file_get_contents($colorPathBack[$colorId])
                            )
                        ]
                    ];
                    Shopify::i($store->shopifyDomain(), $store->access_token)
                        ->addProductImages($shopifyProductId, $imageData);
                    unset($imageData);
                }
            }
        }
        else {

            $previewPath = null;
            if ($product->mockupPreview() && $product->mockupPreview()->file) {
                $previewPath = $product->mockupPreview()->file->path();
            }
            else if ($product->mockupPreviewBack() && $product->mockupPreviewBack()->file) {
                $previewPath = $product->mockupPreviewBack()->file->path();
            }

            $imageData = [
                'image' => [
                    'variant_ids' => $allVariantsProviderIds,
                    'attachment' => base64_encode(
                        file_get_contents(
                            $previewPath
                        )
                    )
                ]
            ];
            Shopify::i($store->shopifyDomain(), $store->access_token)
                ->addProductImages($shopifyProductId, $imageData);
        }
    }

}
