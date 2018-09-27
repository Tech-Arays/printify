<?php

namespace App\Transformers\CatalogAttribute;

use League\Fractal;

use App\Models\CatalogAttribute;
use App\Transformers\CatalogAttribute\CatalogAttributeOptionTransformer;

class CatalogAttributeTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'options'
    ];
    
    public function includeOptions(CatalogAttribute $attr)
    {
        $options = $attr->catalogOptions;
        if ($options) {
            return $this->collection($options, new CatalogAttributeOptionTransformer);
        }
    }
    
	public function transform(CatalogAttribute $attr)
	{
	    return [
	        'id'         => (int)$attr->id,
	        'name'       => $attr->name,
            'value'      => $attr->value,
            'option_ids' => $attr->catalogOptions->pluck('id')
	    ];
	}
}
