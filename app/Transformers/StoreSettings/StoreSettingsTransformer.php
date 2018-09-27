<?php

namespace App\Transformers\StoreSettings;

use App\Transformers\Transformer;
use App\Models\StoreSettings;

class StoreSettingsTransformer extends Transformer
{
	public function transform(StoreSettings $settings)
	{
        $allowedList = [
            StoreSettings::SETTING_AUTO_ORDERS_CONFIRM,
            StoreSettings::SETTING_AUTO_PUSH_PRODUCTS,
            StoreSettings::SETTING_CARD_CHARGE_LIMIT_ENABLED,
            StoreSettings::SETTING_CARD_CHARGE_LIMIT_AMOUNT,
            StoreSettings::SETTING_CARD_CHARGE_CHARGES_AMOUNT
        ];
        
        $result = [];
	    foreach($allowedList as $allowed) {
            $result[$allowed] = $settings->{$allowed};
        }
    
        return $result;
	}
}
