<?php

namespace App\Listeners\Cache;

use Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\ProductModelTemplate;

class OnProductModelTemplatesClearCacheEvent
{
    public function handle($event)
    {
        $templates = [];
        if (isset($event->product_model_template) && $event->product_model_template) {
            $templates[] = $event->product_model_template;
        }
        else if (isset($event->product_model) && $event->product_model) {
            $templates[] = $event->product_model->template;
        }
        else if (isset($event->price_modifier) && $event->price_modifier) {
            $templates[] = $event->price_modifier->template;
        }
        else {
            $templates = ProductModelTemplate::get();
        }

        if (!empty($templates)) {
            foreach($templates as $template) {
                Cache::forget(
                    \App\Http\Controllers\Dashboard\ProductsController::class.':getProductModelTemplate:'.$template->id
                );
            }
        }
    }
}
