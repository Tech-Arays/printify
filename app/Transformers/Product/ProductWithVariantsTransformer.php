<?php

namespace App\Transformers\Product;

use App\Models\Product;
use App\Transformers\Transformer;
use App\Transformers\ProductClientFile\ProductClientFileIncludedTransformer;
use App\Transformers\ProductVariant\ProductVariantIncludedToProductTransformer;
use App\Transformers\File\ImageFileTransformer;

class ProductWithVariantsTransformer extends Transformer
{
    protected $defaultIncludes = [
        'variants',
        'mockupPreview'
    ];
    
    public function includeVariants(Product $product)
    {
        $variants = $product->variants;
        if ($variants) {
            return $this->collection($variants, new ProductVariantIncludedToProductTransformer);
        }
    }
    
    public function includeMockupPreview(Product $product)
    {
        $file = $product->mockupPreview();
        if ($file) {
            return $this->item($file, new ImageFileTransformer);
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
