<?php

namespace App\Jobs\ProductVariant;

use Log;
use Exception;
use Bugsnag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Job;
use App\Models\ProductVariant;
use App\Components\Shopify;

class UpdateShopifyVariantJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $variant;
    protected $data = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ProductVariant $variant, $data)
    {
        $this->variant = $variant;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $variant = $this->variant;
        $data = $this->data;

        if (!$variant->product || !$variant->product->store) return;

        $store = $variant->product->store;

        // push products
        try {
            Shopify::i($store->shopifyDomain(), $store->access_token)
                ->updateVariant($variant->provider_variant_id, $data);
        }
        catch(Exception $e) {
            if (Shopify::is404Exception($e)) {
                // product doesn't exist on shopify, do nothing
            }
            else if (Shopify::is402Exception($e)) {
                // store is suspended, do nothing
            }
            else {
                $logMetadata = [
                    'variant' => $variant,
                    'data' => $data
                ];
                Log::error('Update shopify variant exception: '.$e->getMessage().' Stack trace: '.$e->getTraceAsString(), [
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
        }
    }
}
