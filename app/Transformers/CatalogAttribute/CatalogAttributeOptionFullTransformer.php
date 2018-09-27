<?php

namespace App\Transformers\CatalogAttribute;

use League\Fractal;

use App\Models\CatalogAttributeOption;
use App\Transformers\CatalogAttribute\CatalogAttributeIncludedTransformer;

class CatalogAttributeOptionFullTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'attribute'
    ];
    
    public function includeAttribute(CatalogAttributeOption $option)
    {
        $attr = $option->catalogAttribute;
        if ($attr) {
            return $this->item($attr, new CatalogAttributeIncludedTransformer);
        }
    }
    
	public function transform(CatalogAttributeOption $option)
	{
	    return [
	        'id'           => (int)$option->id,
	        'name'         => $option->name,
            'value'        => $option->value,
            'kz_option_id' => (int)$option->kz_option_id
	    ];
	}
}
