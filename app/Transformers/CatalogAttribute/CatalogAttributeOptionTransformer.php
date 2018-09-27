<?php

namespace App\Transformers\CatalogAttribute;

use League\Fractal;

use App\Models\CatalogAttributeOption;

class CatalogAttributeOptionTransformer extends Fractal\TransformerAbstract
{
	public function transform(CatalogAttributeOption $option)
	{
	    return [
	        'id'        => (int)$option->id,
	        'name'      => $option->name,
            'value'     => $option->value
	    ];
	}
}
