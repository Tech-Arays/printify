<?php

namespace App\Transformers\Product;

use League\Fractal;

use App\Models\ProductModelTemplate;
use App\Transformers\Transformer;
use App\Transformers\Garment\GarmentIncludedTransformer;
use App\Transformers\CatalogCategory\CatalogCategoryIncludedTransformer;
use App\Transformers\ProductModel\ProductModelIncludedTransformer;

class ProductModelTemplateFullTransformer extends Transformer
{
    protected $defaultIncludes = [
        'models',
        'category'
    ];

    public function includeModels(ProductModelTemplate $model)
    {
        $models = $model->models;
        if ($models) {
            return $this->collection($models, new ProductModelIncludedTransformer);
        }
        else {
            return $this->null();
        }
    }

    public function includeCategory(ProductModelTemplate $model)
    {
        $category = $model->category;
        if ($category) {
            return $this->item($category, new CatalogCategoryIncludedTransformer);
        }
        else {
            return $this->null();
        }
    }

    public function includeGarment(ProductModelTemplate $template)
    {
        if ($template->garment) {
            return $this->item($template->garment, new GarmentIncludedTransformer);
        }
    }

	private function childOptions($model, $attrs)
	{
		$singleTree = [];
		if ($attrs->isEmpty()) {
			return $singleTree;
		}

		do {
			$attr = $attrs->shift();
			$options = $model->optionsOfAttribute($attr->value)
				->with('catalogAttribute')
				->get();

		} while(!$attrs->isEmpty() && $options->isEmpty());

		foreach ($options as $option) {
            $childOptions = $this->childOptions($model, $attrs);

            $singleTree[$option->id] = array_merge($option->transformFull(), [
				'children'     => $childOptions
			]);

            if (empty($childOptions)) {
                $singleTree[$option->id]['model_id'] = $model->id;
            }
		}

		return $singleTree;
	}

    protected function optionsTree($models, $originalAttrs)
    {
        $optionsTree = [];
        foreach($models as $model) {
            $attrs = clone $originalAttrs;
            $singleTree = $this->childOptions($model, $attrs);
            $optionsTree = array_replace_recursive($optionsTree, $singleTree);
        }

        return $optionsTree;
    }

	public function transform(ProductModelTemplate $template)
	{
        $models = $template->models;
        $attrs = $template->catalogAttributes();

        $optionsTree = $this->optionsTree($models, $attrs);

        // TODO: DEPRECATED
        //$attrsTree = $this->attributesTree($models, $attrs);

	    return [
	        'id'                           => $this->getId($template),
	        'name'                         => $template->name,
            'price'                        => $template->price,
            'priceMoney'                   => $template->priceMoney(),
            'product_title'                => $template->product_title,
            'product_description'          => $template->product_description,
            'preview'                      => ($template->preview ? url($template->preview->url('thumb')) : null),
            'image'                        => ($template->image ? url($template->image->url()) : null),
            'imageBack'                    => ($template->imageBack ? url($template->imageBack->url()) : null),
            'backPrintCanBeAddedOnItsOwn'  => $template->backPrintCanBeAddedOnItsOwn(),
            'catalogAttributes'            => $attrs,

            // TODO: DEPRECATED
            //'attributesTree'  		   => $attrsTree,

			'optionsTree'  			       => $optionsTree
	    ];
	}
}
