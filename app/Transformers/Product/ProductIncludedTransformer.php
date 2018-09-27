<?php

namespace App\Transformers\Product;

use League\Fractal;

use App\Transformers\Transformer;
use App\Models\Product;

class ProductIncludedTransformer extends Transformer
{
    protected $defaultIncludes = [
        
    ];
    
	public function transform(Product $product)
	{
	    return [
	        'id'          => $this->getId($product),
	        'name'        => $product->name,
            'status'      => $product->status
	    ];
	}
}
