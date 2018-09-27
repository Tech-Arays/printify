<?php

namespace App\Transformers\File;

use App\Models\File;
use App\Transformers\Transformer;

class ProductVariantFileTransformer extends Transformer
{
    protected $defaultIncludes = [

    ];

	public function transform(File $file)
	{
	    return [
	        'id'          => (int)$file->id,
            'type'        => $file->pivot->type,
	        'typeName'    => $file->getTypeName(),
            'thumb'       => url($file->url('medium')),
            'url'         => url($file->url()),
            'policy'      => $this->includePolicies($file)
	    ];
	}
}
