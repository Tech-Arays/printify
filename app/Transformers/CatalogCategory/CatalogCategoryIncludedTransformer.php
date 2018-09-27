<?php

namespace App\Transformers\CatalogCategory;

use League\Fractal;

use App\Models\CatalogCategory;
use App\Transformers\Product\ProductModelTemplateIncludedTransformer;

class CatalogCategoryIncludedTransformer extends Fractal\TransformerAbstract
{
    public function includeChildren(CatalogCategory $category)
    {
        $children = $category->immediateDescendants()->get();
        if ($children) {
            return $this->collection($children, new CatalogCategoryTransformer);
        }
    }

	public function includeTemplates(CatalogCategory $category)
    {
        $models = $category->templates()->complete()->visible()->get();

        if ($models) {
            return $this->collection($models, new ProductModelTemplateIncludedTransformer);
        }
        else {
            return $this->null();
        }
    }

	public function transform(CatalogCategory $category)
	{
	    return [
	        'id'                 => (int)$category->id,
	        'name'               => $category->name(),
            'slug'               => $category->slug,
            'isPrepaid'          => $category->isPrepaid(),
            'prepaid_amount'     => $category->prepaid_amount,
            'prepaidAmountMoney' => $category->prepaidAmountMoney(),
            'preview'            => ($category->preview ? $category->preview->url() : null),
	    ];
	}
}
