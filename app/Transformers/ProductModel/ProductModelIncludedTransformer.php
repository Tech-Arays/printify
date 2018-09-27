<?php

namespace App\Transformers\ProductModel;

use League\Fractal;

use App\Components\Money;
use App\Models\ProductModel;
use App\Transformers\Transformer;
use App\Transformers\CatalogAttribute\CatalogAttributeOptionFullTransformer;
use App\Transformers\Product\ProductModelTemplateWithCategoriesTransformer;

class ProductModelIncludedTransformer extends Transformer
{
    protected $defaultIncludes = [
        'options'
    ];

    public function includeOptions(ProductModel $model)
    {
        $options = $model->catalogOptions;
        if ($options) {
            return $this->collection($options, new CatalogAttributeOptionFullTransformer);
        }
    }

    public function includeTemplate(ProductModel $model)
    {
        $template = $model->template;
        if ($template) {
            return $this->item($template, new ProductModelTemplateWithCategoriesTransformer);
        }
    }

	public function transform(ProductModel $model)
	{
	    return [
            'id'                   => $this->getId($model),
	        'price'                => Money::i()->amount($model->frontPrice()),
            'priceMoney'           => $model->frontPrice(),
            'frontPrice'           => Money::i()->amount($model->frontPrice()),
            'frontPriceMoney'      => $model->frontPrice(),
            'backPrice'            => Money::i()->amount($model->backPrice()),
            'backPriceMoney'       => $model->backPrice(),
            'bothSidesPrice'       => Money::i()->amount($model->bothSidesPrice()),
            'bothSidesPriceMoney'  => $model->bothSidesPrice(),
            'inventory_status'     => $model->inventory_status
	    ];
	}
}
