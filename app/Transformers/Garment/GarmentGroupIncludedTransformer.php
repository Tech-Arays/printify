<?php

namespace App\Transformers\Garment;

use League\Fractal;

use App\Models\GarmentGroup;

class GarmentGroupIncludedTransformer extends Fractal\TransformerAbstract
{
	public function transform(GarmentGroup $group)
	{
	    return [
	        'id'       => $group->id,
            'name'     => $group->name,
            'slug'     => $group->slug,
            'position' => $group->position
	    ];
	}
}
