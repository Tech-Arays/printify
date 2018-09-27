<?php

namespace App\Transformers\File;

use League\Fractal;

use App\Models\File;
use App\Transformers\Transformer;

class FileFullTransformer extends Transformer
{
    protected $defaultIncludes = [

    ];

	public function transform($file)
	{


	    return [
	        'id'          => (int)$file->id,
            'type'        => $file->type,
	        'typeName'    => $file->getTypeName(),
            'url'         => url($file->url()),
            'thumb'       => url($file->url('medium')),
			'name'        => $file->file_file_name,
			'size'        => $file->file_file_size,
			'updated'     => $file->updatedAtTZ(),
            'policy'      => $this->includePolicies($file)
	    ];
	}
}
