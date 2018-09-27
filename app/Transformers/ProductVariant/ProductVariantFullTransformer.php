<?php

namespace App\Transformers\ProductVariant;

use League\Fractal;

use App\Models\ProductVariant;
use App\Transformers\Transformer;
use App\Transformers\ProductModel\ProductModelFullTransformer;
use App\Transformers\Product\ProductIncludedTransformer;
use App\Transformers\File\ProductVariantFileTransformer;

class ProductVariantFullTransformer extends Transformer
{
    protected $defaultIncludes = [
		'model',

        // TODO: not needed for now
        //'files',

        'mockups',
        'product'
    ];

    public function includeProduct(ProductVariant $variant)
    {
        $model = $variant->product;
        if ($model) {
            return $this->item($model, new ProductIncludedTransformer);
        }
    }

	public function includeModel(ProductVariant $variant)
    {
        $model = $variant->productModel;
        if ($model) {
            return $this->item($model, new ProductModelFullTransformer);
        }
    }

    // TODO: not needed for now
    //public function includeFiles(ProductVariant $variant)
    //{
    //    $files = $variant->files;
    //    if ($files) {
    //        return $this->collection($files, new ProductVariantFileTransformer);
    //    }
    //    else {
    //        return $this->null();
    //    }
    //}

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
	        'id'                     => $this->getId($variant),
	        'name'                   => $variant->name,
            'status'                 => $variant->status,
            'quantity'               => $variant->quantity(),
            'printPriceMoney'        => $variant->printPrice(),
            'customerPaidPriceMoney' => $variant->customerPaidPrice()
	    ];
	}
}
