<?php

namespace App\Jobs\Store;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Components\Shopify;
use App\Jobs\Job;
use App\Models\Store;

class StoreConnectJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $store;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Shopify::i($this->store->shopifyDomain(), $this->store->access_token)
            ->replaceWebhooks();

        // cache webhooks statuses
        $this->store->shopifyWebhooksAreSetUp(Store::$CACHE_FORCE_UPDATE);
    }
}
