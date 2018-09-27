<?php

namespace App\Transformers\Store;

use App\Transformers\Transformer;
use App\Models\Store;

class StoreBriefTransformer extends Transformer
{
	public function transform(Store $store)
	{
	    return [
	        'id'            => $store->id,
	        'name'          => $store->name,
            'isInSync'      => $store->isInSync(),
            'productsCount' => count($store->vendorProducts)
	    ];
	}
}
