<?php

namespace App\Transformers\Product;

use League\Fractal;

use App\Models\ProductModelTemplate;
use App\Transformers\Transformer;
use App\Transformers\Garment\GarmentIncludedTransformer;

class ProductModelTemplateIncludedTransformer extends Transformer
{
    protected $defaultIncludes = [
        'garment'
    ];

    public function includeGarment(ProductModelTemplate $template)
    {
        if ($template->garment) {
            return $this->item($template->garment, new GarmentIncludedTransformer);
        }
    }

	public function transform(ProductModelTemplate $template)
	{
	    return [
	        'id'                           => $this->getId($template),
	        'name'                         => preg_replace('/(\s(Guy|Girl|Infant))$/', '', $template->name),
            'price'                        => $template->price,
            'priceMoney'                   => $template->priceMoney(),
            'isPrepaid'                    => $template->isPrepaid(),
                'prepaid_amount'               => $template->isPrepaid()
                    ? $template->category->prepaid_amount
                    : null,
                'prepaidAmountMoney'               => $template->isPrepaid()
                    ? $template->category->prepaidAmountMoney()
                    : null,
            'product_title'                => $template->product_title,
            'product_description'          => $template->product_description,
            'preview'                      => ($template->preview ? url($template->preview->url('thumb')) : null),
            'image'                        => ($template->image ? url($template->image->url()) : null),
            'imageBack'                    => ($template->imageBack ? url($template->imageBack->url()) : null),
            'backPrintCanBeAddedOnItsOwn'  => $template->backPrintCanBeAddedOnItsOwn()
	    ];
	}
}
