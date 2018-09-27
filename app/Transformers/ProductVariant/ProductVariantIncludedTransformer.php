<?php

namespace App\Transformers\ProductVariant;

use League\Fractal;

use App\Transformers\Transformer;
use App\Models\ProductVariant;

class ProductVariantIncludedTransformer extends Transformer
{
    protected $defaultIncludes = [
    ];
    
	public function transform(ProductVariant $variant)
	{
	    return [
	        'id'     => $this->getId($variant),
	        'name'   => $variant->name,
            'status' => $variant->status
	    ];
	}
}
