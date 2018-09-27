<?php

namespace App\Transformers\Product;

use League\Fractal;

use App\Components\Money;
use App\Models\Product;
use App\Transformers\Transformer;
use App\Transformers\ProductClientFile\ProductClientFileIncludedTransformer;
use App\Transformers\ProductVariant\ProductVariantIncludedToProductTransformer;

class ProductEditingTransformer extends Transformer
{
    protected $defaultIncludes = [
        'clientFiles',
        'template',
        'variants'
    ];

    protected function getSelectedModelIdsAndPrices($product)
    {
        $retailPrices = [];
        $models = [];

        foreach ($product->variants as $variant) {
            if ($variant->model) {
                $models[] = $variant->model->transformIncluded();
                $retailPrices[$variant->model->id] = Money::i()->amount($variant->retailPrice());
            }
        }

        return [
            $models,
            $retailPrices
        ];
    }

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

    public function includeTemplate(Product $model)
    {
        $template = $model->variants->first()->model->template;
        if ($template) {
            return $this->item($template, new ProductModelTemplateWithCategoriesTransformer);
        }
    }

	public function transform(Product $product)
	{
        list($models, $retailPrices) = $this->getSelectedModelIdsAndPrices($product);

	    return [
	        'id'                        => $this->getId($product),
	        'name'                      => $product->name,
            'status'                    => $product->status,
            'moderation_status'         => $product->moderation_status,
            'moderationStatusName'      => $product->getModerationStatusName(),
            'moderation_status_comment' => $product->moderation_status_comment,
            'meta'                      => $product->meta,
            'canvas_meta'               => $product->canvas_meta,
            'retailPrices'              => $retailPrices,
            'models'                    => $models,
            'policy'                    => $this->includePolicies($product)
	    ];
	}
}
