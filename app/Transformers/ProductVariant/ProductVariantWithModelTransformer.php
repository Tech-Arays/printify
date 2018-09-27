<?php

namespace App\Transformers\ProductVariant;

use League\Fractal;

use App\Models\ProductVariant;
use App\Transformers\Transformer;
use App\Transformers\ProductModel\ProductModelIncludedTransformer;
use App\Transformers\Product\ProductIncludedTransformer;
use App\Transformers\File\ProductVariantFileTransformer;

class ProductVariantWithModelTransformer extends Transformer
{
    protected $defaultIncludes = [
		'model',
        'mockups'
    ];

	public function includeModel(ProductVariant $variant)
    {
        $model = $variant->productModel;
        if ($model) {
            return $this->item($model, new ProductModelIncludedTransformer);
        }
    }

    public function includeMockups(ProductVariant $variant)
    {
        $files = $variant->mockups;
        if ($files) {
            return $this->collection($files, new ProductVariantFileTransformer);
        }
        else {
            return $this->null();
        }
    }

	public function transform(ProductVariant $variant)
	{
	    return [
	        'id'     => $this->getId($variant),
	        'name'   => $variant->name,
            'status' => $variant->status
	    ];
	}
}
