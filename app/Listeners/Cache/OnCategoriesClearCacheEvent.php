<?php

namespace App\Listeners\Cache;

use Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnCategoriesClearCacheEvent
{
    public function handle($event)
    {
        Cache::forget(
			\App\Http\Controllers\Dashboard\ProductsController::class.':getCategories'
		);
    }
}

