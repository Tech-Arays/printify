<?php

namespace App\Transformers\Garment;

use League\Fractal;

use App\Models\Garment;

use App\Transformers\Garment\GarmentGroupIncludedTransformer;

class GarmentIncludedTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'garmentGroup'
    ];

    public function includeGarmentGroup(Garment $garment)
    {
        if ($garment->garmentGroup) {
            return $this->item($garment->garmentGroup, new GarmentGroupIncludedTransformer);
        }
    }

	public function transform(Garment $garment)
	{
	    return [
	        'id'                       => $garment->id,
            'name'                     => $garment->name,
            'slug'                     => $garment->slug,
            'position'                 => $garment->position,
            'preview'                  => ($garment->preview ? url($garment->preview->url('thumb')) : null),
            'isAllOverPrint'           => $garment->isAllOverPrint(),
            'isAllOverPrintOrSimilar'  => $garment->isAllOverPrintOrSimilar(),
	    ];
	}
}
