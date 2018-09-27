<?php

namespace App\Transformers\Order;

use League\Fractal;

use App\Models\Order;
use App\Transformers\ProductVariant\ProductVariantFullTransformer;
use App\Transformers\Store\StoreBriefTransformer;

class OrderFullTransformer extends OrderBriefTransformer
{
    protected $defaultIncludes = [
		'variants',
        'store'
    ];
	
    public function includeVariants(Order $order)
    {
        $variants = $order->variants;
        if ($variants) {
            return $this->collection($variants, new ProductVariantFullTransformer);
        }
    }
    
    public function includeStore(Order $order)
    {
        $store = $order->store;
        if ($store) {
            return $this->item($store, new StoreBriefTransformer);
        }
    }
}
