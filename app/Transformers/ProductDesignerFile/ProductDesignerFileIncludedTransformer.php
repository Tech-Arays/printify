<?php

namespace App\Transformers\ProductDesignerFile;

use League\Fractal;

use App\Models\ProductDesignerFile;
use App\Transformers\File\FileFullTransformer;
use App\Transformers\ProductVariant\ProductVariantWithModelTransformer;

class ProductDesignerFileIncludedTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'file',
        'variants'
    ];

    public function includeFile(ProductDesignerFile $designerFile)
    {
        $file = $designerFile->file;
        if ($file) {
            return $this->item($file, new FileFullTransformer);
        }
        else {
            return $this->null();
        }
    }

    public function includeVariants(ProductDesignerFile $designerFile)
    {
        $variants = $designerFile->variants;
        if ($variants) {
            return $this->collection($variants, new ProductVariantWithModelTransformer);
        }
    }

	public function transform(ProductDesignerFile $designerFile)
	{
	    return [
	        'id'          => $designerFile->id
	    ];
	}
}
