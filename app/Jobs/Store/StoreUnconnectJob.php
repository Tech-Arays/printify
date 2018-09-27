<?php

namespace App\Jobs\Store;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Components\Shopify;
use App\Jobs\Job;
use App\Models\Store;

class StoreUnconnectJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $store_domain;
    protected $store_access_token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($store_domain, $store_access_token)
    {
        $this->store_domain = $store_domain;
        $this->store_access_token = $store_access_token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Shopify::i($this->store_domain, $this->store_access_token)
            ->removeAllWebhooks();
    }
}
