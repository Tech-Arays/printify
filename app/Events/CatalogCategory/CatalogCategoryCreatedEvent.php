<?php

namespace App\Events\CatalogCategory;

use App\Events\Event;
use App\Models\CatalogCategory;

class CatalogCategoryCreatedEvent extends Event
{
    public $catalogCategory;

    public function __construct(CatalogCategory $category)
    {
        $this->catalogCategory = $category;
    }
}
