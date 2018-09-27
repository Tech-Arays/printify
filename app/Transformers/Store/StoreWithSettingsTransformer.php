<?php

namespace App\Transformers\Store;

use App\Models\Store;
use App\Transformers\StoreSettings\StoreSettingsTransformer;

class StoreWithSettingsTransformer extends StoreBriefTransformer
{
    public $defaultIncludes = [
        'settings'
    ];
    
    public function includeSettings(Store $store)
    {
        $settings = $store->settings;
        if ($settings) {
            return $this->item($settings, new StoreSettingsTransformer);
        }
    }
    
	public function transform(Store $store)
	{
	    return [
	        'id'     => $store->id,
	        'name'   => $store->name,
            'productsCount' => count($store->vendorProducts)
	    ];
	}
}
