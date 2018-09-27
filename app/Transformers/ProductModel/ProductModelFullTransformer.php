<?php

namespace App\Transformers\ProductModel;

use App\Models\ProductModel;

class ProductModelFullTransformer extends ProductModelIncludedTransformer
{
    protected $defaultIncludes = [
        'options',
        'template'
    ];
}
