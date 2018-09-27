<?php

namespace App\Transformers\File;

use App\Models\File;
use App\Transformers\Transformer;

class ImageFileFullTransformer extends Transformer
{
    protected $defaultIncludes = [

    ];

	public function transform(File $file)
	{
	    return [
	        'id'          => (int)$file->id,
            'type'        => $file->type,
	        'typeName'    => $file->getTypeName(),
            'name'        => $file->file_file_name,
			'size'        => $file->file_file_size,
			'updated'     => $file->updatedAtTZ(),
            'thumb'       => url($file->url('medium')),
            'url'         => url($file->url()),
            'dimensions'  => $file->getDimensions(),
            'policy'      => $this->includePolicies($file)
	    ];
	}
}
