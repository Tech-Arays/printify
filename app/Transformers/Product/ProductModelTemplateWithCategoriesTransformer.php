<?php

namespace App\Transformers\Product;

use League\Fractal;

use App\Models\ProductModelTemplate;
use App\Transformers\Transformer;
use App\Transformers\CatalogCategory\CatalogCategoryTransformer;

class ProductModelTemplateWithCategoriesTransformer extends Transformer
{
    protected $defaultIncludes = [
        'category'
    ];

    public function includeCategory(ProductModelTemplate $model)
    {
        $category = $model->category;
        if ($category) {
            return $this->item($category, new CatalogCategoryTransformer);
        }
        else {
            return $this->null();
        }
    }

	public function transform(ProductModelTemplate $template)
	{
        $attrs = $template->catalogAttributes();

	    return [
	        'id'                           => $this->getId($template),
	        'name'                         => $template->name,
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
            'backPrintCanBeAddedOnItsOwn'  => $template->backPrintCanBeAddedOnItsOwn(),
            'catalogAttributes'            => $attrs
	    ];
	}
}
