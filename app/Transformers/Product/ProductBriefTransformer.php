<?php

namespace App\Transformers\Product;

use League\Fractal;

use App\Models\Product;
use App\Transformers\Transformer;
use App\Transformers\ProductClientFile\ProductClientFileIncludedTransformer;
use App\Transformers\ProductVariant\ProductVariantIncludedToProductTransformer;

class ProductBriefTransformer extends Transformer
{
	public function transform(Product $product)
	{
	    return [
	        'id'          => $this->getId($product),
	        'name'        => $product->name,
            'status'      => $product->status
	    ];
	}
}
