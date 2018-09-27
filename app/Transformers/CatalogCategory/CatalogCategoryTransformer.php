<?php

namespace App\Transformers\CatalogCategory;

use League\Fractal;

use App\Models\CatalogCategory;

class CatalogCategoryTransformer extends CatalogCategoryIncludedTransformer
{
    protected $defaultIncludes = [
        'children',
		'templates'
    ];
}
