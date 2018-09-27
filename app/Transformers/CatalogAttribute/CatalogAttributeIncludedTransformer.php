<?php

namespace App\Transformers\CatalogAttribute;

use League\Fractal;

use App\Models\CatalogAttribute;

class CatalogAttributeIncludedTransformer extends Fractal\TransformerAbstract
{

	public function transform(CatalogAttribute $attr)
	{
	    return [
	        'id'         => (int)$attr->id,
	        'name'       => $attr->name,
            'value'      => $attr->value,
	    ];
	}
}
