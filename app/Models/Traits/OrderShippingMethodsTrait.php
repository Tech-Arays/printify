<?php

namespace App\Models\Traits;

use App\Components\Money;

trait OrderShippingMethodsTrait
{

    private static function toPounds($ounce)
    {
        return (string)($ounce * 0.0625);
    }

    /*************
     * Checks
     */

        public function isShippingMethodSelected()
        {
            return (bool)$this->shipping_method;
        }

    /*************
     * Decorators
     */

        public function getListOfShippingMethods()
        {
            return static::listShippingMethods();
        }

    /*************
     * Helpers
     */

        public static function listShippingMethods()
        {
            return collect([
                static::SHIPPING_METHOD_FIRST_CLASS => trans('labels.shipping_method_first_class'),
                static::SHIPPING_METHOD_PRIORITY_MAIL => trans('labels.shipping_method_priority_mail')
            ]);
        }

        public static function guessShippingMethodByShopifyData($shopifyJson)
        {
            $shippingCode = !empty($shopifyJson->shipping_lines) && !empty($shopifyJson->shipping_lines[0])
                ? $shopifyJson->shipping_lines[0]->code
                : null;

            $shippingTitle = !empty($shopifyJson->shipping_lines) && !empty($shopifyJson->shipping_lines[0])
                ? $shopifyJson->shipping_lines[0]->title
                : null;

            $shippingMethod = null;

            if (
                (stristr($shippingCode, 'first') && stristr($shippingCode, 'class'))
                || (stristr($shippingTitle, 'first') && stristr($shippingTitle, 'class'))
            ) {
                $shippingMethod = static::SHIPPING_METHOD_FIRST_CLASS;
            }
            else if (
                (stristr($shippingCode, 'priority') && stristr($shippingCode, 'mail'))
                || (stristr($shippingTitle, 'priority') && stristr($shippingTitle, 'mail'))
            ) {
                $shippingMethod = static::SHIPPING_METHOD_PRIORITY_MAIL;
            }

            return $shippingMethod;
        }

        public function getShippingMethodName()
        {
            $methods = static::listShippingMethods();
            return isset($methods[$this->shipping_method]) ? $methods[$this->shipping_method] : null;
        }

        public function getShippingMethodKZ()
        {
            $method = null;

            switch($this->shipping_method) {
                case static::SHIPPING_METHOD_FIRST_CLASS:
                    if ($this->shipsToUS()) {
                        $method = static::SHIPPING_METHOD_KZ_FIRST_CLASS;
                    }
                    else {
                        $method = static::SHIPPING_METHOD_KZ_FIRST_CLASS_INTL;
                    }
                    break;

                case static::SHIPPING_METHOD_PRIORITY_MAIL:
                    if ($this->shipsToUS()) {
                        $method = static::SHIPPING_METHOD_KZ_PRIORITY_MAIL;
                    }
                    else {
                        $method = static::SHIPPING_METHOD_KZ_PRIORITY_MAIL_INTL;
                    }
                    break;
            }

            return $method;
        }

        /**
         * Some kind of entry point for shipping price
         */
        public function getShippingPrice($shippingMethod = null)
        {
            if (!$shippingMethod) {
                $shippingMethod = $this->shipping_method;
            }

            // separate variants by grous,
            // find the biggest full price for country
            $biggestFullPrice = Money::i()->parse(0);
            $biggestFullPriceVariant = null;
            $allShippingGroups = [];
            $variantsByShippingGroup = [];
            foreach($this->variants as $variant) {
                if (
                    $variant->model
                    && $variant->model->template
                    && $variant->model->template->shippingGroups
                    && !$variant->model->template->shippingGroups->isEmpty()
                ) {
                    $shippingGroups = $variant->model->template->shippingGroups;
                    foreach($shippingGroups as $shippingGroup) {
                        $allShippingGroups[$shippingGroup->id] = $shippingGroup;
                        $variantsByShippingGroup[$shippingGroup->id][] = $variant;

                        // choose biggest price
                            if ($this->shipsToUS()) {
                                $currentFullPrice = $shippingGroup->fullPriceUS($shippingMethod);
                            }
                            else if ($this->shipsToCanada()) {
                                $currentFullPrice = $shippingGroup->fullPriceCanada($shippingMethod);
                            }
                            else {
                                $currentFullPrice = $shippingGroup->fullPriceIntl($shippingMethod);
                            }

                            if ($biggestFullPrice->lessThan($currentFullPrice)) {
                                $biggestFullPrice = $currentFullPrice;
                                $biggestFullPriceVariant = $variant;
                            }
                    }
                }
            }

            $shippingCost = Money::i()->parse(0);
            $shippingCost = $shippingCost->add($biggestFullPrice);

            // add additional price
            if ($variantsByShippingGroup) {
                foreach($variantsByShippingGroup as $shippingGroupId => $variants) {
                    $qty = 0;

                    if (!empty($variants)) {
                        foreach($variants as $variant) {
                            $qty += $variant->quantity();

                            // skip the only one first biggest price variant
                            if (
                                $biggestFullPriceVariant
                                && $variant->id == $biggestFullPriceVariant->id
                            ) {
                                $biggestFullPriceVariant = null;
                                $qty -= 1;
                            }
                        }
                    }

                    if ($this->shipsToUS()) {
                        $currentAdditionalPrice = $allShippingGroups[$shippingGroupId]
                            ->additionalPriceUS($shippingMethod);
                    }
                    else if ($this->shipsToCanada()) {
                        $currentAdditionalPrice = $allShippingGroups[$shippingGroupId]
                            ->additionalPriceCanada($shippingMethod);
                    }
                    else {
                        $currentAdditionalPrice = $allShippingGroups[$shippingGroupId]
                            ->additionalPriceIntl($shippingMethod);
                    }

                    if ($qty > 0) {
                        $currentAdditionalPrice = $currentAdditionalPrice->multiply($qty);

                        $shippingCost = $shippingCost->add($currentAdditionalPrice);
                    }
                }
			}

            return $shippingCost;
        }

        public function autoSelectShippingMethod()
        {
            $this->shipping_method = static::SHIPPING_METHOD_FIRST_CLASS;
            $this->save();
        }
}
