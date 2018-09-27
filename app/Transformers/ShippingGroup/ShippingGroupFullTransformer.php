<?php

namespace App\Transformers\ShippingGroup;

use League\Fractal;

use App\Models\ShippingGroup;
use App\Transformers\Product\ProductModelTemplateWithCategoriesTransformer;

class ShippingGroupFullTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
		'templates'
    ];
    
    public function includeTemplates(ShippingGroup $group)
    {
        $templates = $group->templates;
        if ($templates) {
            return $this->collection($templates, new ProductModelTemplateWithCategoriesTransformer);
        }
    }
    
	public function transform(ShippingGroup $group)
	{
	    return [
	        'id'     => (int)$group->id,
	        'name'   => $group->name
	    ];
	}
}
