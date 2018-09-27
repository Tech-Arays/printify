<?php

namespace App\Transformers\ProductClientFile;

use League\Fractal;

use App\Models\ProductClientFile;
use App\Transformers\File\ImageFileTransformer;
use App\Transformers\File\FileFullTransformer;
use App\Transformers\ProductDesignerFile\ProductDesignerFileIncludedTransformer;

class ProductClientFileIncludedTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'designerFiles',
        'mockup',
        'printFile',
        'sourceFile'
    ];

    public function includeMockup(ProductClientFile $clientFile)
    {
        $file = $clientFile->mockup;
        if ($file) {
            return $this->item($file, new ImageFileTransformer);
        }
    }

    public function includePrintFile(ProductClientFile $clientFile)
    {
        $file = $clientFile->printFile;
        if ($file) {
            return $this->item($file, new ImageFileTransformer);
        }
    }

    public function includeSourceFile(ProductClientFile $clientFile)
    {
        $file = $clientFile->sourceFile;
        if ($file) {
            return $this->item($file, new FileFullTransformer);
        }
    }

    public function includeDesignerFiles(ProductClientFile $clientFile)
    {
        $designerFiles = $clientFile->designerFiles;
        if ($designerFiles) {
            return $this->collection($designerFiles, new ProductDesignerFileIncludedTransformer);
        }
    }

	public function transform(ProductClientFile $clientFile)
	{
	    return [
	        'id'              => $clientFile->id,
            'design_location' => $clientFile->design_location
	    ];
	}
}
