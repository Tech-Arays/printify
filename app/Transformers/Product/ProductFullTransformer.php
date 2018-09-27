<?php

namespace App\Transformers\Product;

use App\Models\Product;
use App\Transformers\Transformer;
use App\Transformers\ProductClientFile\ProductClientFileIncludedTransformer;
use App\Transformers\ProductVariant\ProductVariantIncludedToProductTransformer;

class ProductFullTransformer extends Transformer
{
    protected $defaultIncludes = [
        'variants',
        'clientFiles'
    ];
    
    public function includeVariants(Product $product)
    {
        $variants = $product->variants;
        if ($variants) {
            return $this->collection($variants, new ProductVariantIncludedToProductTransformer);
        }
    }
    
    public function includeClientFiles(Product $product)
    {
        $clientFiles = $product->clientFiles;
        if ($clientFiles) {
            return $this->collection($clientFiles, new ProductClientFileIncludedTransformer);
        }
    }
    
	public function transform(Product $product)
	{
	    return [
	        'id'          => $this->getId($product),
	        'name'        => $product->name,
            'status'      => $product->status
	    ];
	}
}
