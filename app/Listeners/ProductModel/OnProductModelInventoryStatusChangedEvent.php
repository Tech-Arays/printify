<?php

namespace App\Listeners\ProductModel;

use Exception;
use Log;
Use Bugsnag;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\Components\Mailer;
use App\Jobs\ProductVariant\UpdateShopifyVariantJob;
use App\Models\Notification;
use App\Models\ProductModel;
use App\Events\ProductModel\ProductModelInventoryStatusChangedEvent;

class OnProductModelInventoryStatusChangedEvent
{
	public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(ProductModelInventoryStatusChangedEvent $event)
    {
		$models = $event->models;
        $inventoryStatus = $event->inventoryStatus;

        $productVariants = [];
        $products = [];

        foreach($models as $model) {
            foreach($model->variants as $variant) {

                if (!$variant->product) continue;

                if (!isset($products[$variant->product_id])) {
                    $products[$variant->product_id] = $variant->product;
                }

                if (!isset($outOfStockProductVariants[$variant->product_id])) {
                    $productVariants[$variant->product_id] = [];
                }

                $productVariants[$variant->product_id][] = $variant;
            }
        }

        foreach($products as $product_id => $product) {
            $variants = $productVariants[$product_id];
            $this->notify($product, $variants, $inventoryStatus);
            $this->updateShopifyInventory($product, $variants, $inventoryStatus);
        }
    }

    protected function notify($product, $variants, $inventoryStatus)
    {
        $user = $product->user;

        switch($inventoryStatus) {
            case ProductModel::INVENTORY_STATUS_OUT_OF_STOCK:

                Mailer::sendProductOutOfStockNotificationEmail($user, [
                    'variants' => $variants
                ]);

                // notification
                    $notification = $this->notifications->create($user, [
                        'icon' => 'fa-exclamation-triangle',
                        'body' => view('widgets.dashboard.notification.out-of-stock', [
                            'variants' => $variants
                        ])->render(),
                        'action_text' => trans('actions.edit_in_shopify'),
                        'action_url' => $product->providerProductEditUrl()
                    ]);

                    $notification->type = Notification::TYPE_ANNOUNCEMENT;
                    $notification->save();

                break;
        }
    }

    protected function updateShopifyInventory($product, $variants, $inventoryStatus)
    {
        // remove variants from shopify
            if ($product->isSynced()) {
                try {
                    foreach ($variants as $variant) {

                        if (!$variant->provider_variant_id) continue;

                        switch($inventoryStatus) {
                            case ProductModel::INVENTORY_STATUS_IN_STOCK:
                                dispatch(new UpdateShopifyVariantJob($variant, [
                                    'inventory_quantity' => 100000000
                                ]));
                                break;

                            case ProductModel::INVENTORY_STATUS_OUT_OF_STOCK:
                                dispatch(new UpdateShopifyVariantJob($variant, [
                                    'inventory_quantity' => 0
                                ]));
                                break;
                        }
                    }
                }
                catch(Exception $e) {
                    if (Shopify::is404Exception($e)) {
                        // product doesn't exist on shopify, do nothing
                    }
                    else {
                        Log::error($e);
                        Bugsnag::notifyException($e);
                        throw $e;
                    }
                }
            }
    }
}
