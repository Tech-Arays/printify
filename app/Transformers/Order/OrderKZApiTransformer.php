<?php

namespace App\Transformers\Order;

use Exception;
use League\Fractal;

use App\Components\Mailer;
use App\Components\Money;
use App\Components\KZApi;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductClientFile;

class OrderKZApiTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected function items(Order $order)
    {
        $items = [];
        foreach($order->variants as $variant) {

            $color = null;
            $size = null;
            if ($variant->model) {
                $color = $variant->model->getColorOption();
                $size = $variant->model->getSizeOption();
                $variantOption = $variant->model->getVariantOption();
            }

            $designerFileFrontChest = $variant->getDesignerFile(
                ProductClientFile::LOCATION_FRONT_CHEST
            );

            if (!$designerFileFrontChest) {
                Mailer::sendOrderProductAdminActionRequiredNotificationEmail([
                    'order' => $order,
                    'product' => $variant->product,
                    'description' => trans('messages.designer_file_undefined_for_{product}', [
                        'product' => $variant->product->id
                    ])
                ]);
            }

            $designerFileBack = $variant->getDesignerFile(
                ProductClientFile::LOCATION_BACK
            );

            if (!$designerFileBack) {
                Mailer::sendOrderProductAdminActionRequiredNotificationEmail([
                    'order' => $order,
                    'product' => $variant->product,
                    'description' => trans('messages.designer_file_undefined_for_{order}', [
                        'product' => $variant->product->id
                    ])
                ]);
            }

            $printSide = '';
            if ($variant->print_side == ProductVariant::PRINT_SIDE_ALL) {
                $printSide = 'A';
            }
            if ($variant->print_side == ProductVariant::PRINT_SIDE_FRONT) {
                $printSide = 'F';
            }
            if ($variant->print_side == ProductVariant::PRINT_SIDE_BACK) {
                $printSide = 'B';
            }

            $items[] = [
                'external_item_id'    => $variant->id, // variant id
                'external_product_id' => $variant->product
                    ? $variant->product->id // mntz product id
                    : null,
                'product_name'        => $variant->product
                    ? $variant->product->name
                    : null,
                'product_type'        => $variant->model
                    ? $variant->model->template->category->name
                    : null,
                'amount'              => $variant->quantity(),
                'price'               => Money::i()->amount($variant->printPrice(1)),
                'garment_variant'     => $variant->model
                    ? $variant->model->template->name
                    : null,
                'size_id'             => ($size ? $size->kz_option_id : null),
                'color_id'            => ($color ? $color->kz_option_id : null),

                'printio_option_id'   => ($variantOption && $variant->model->template->category->isPrintIO())
                    ? $variantOption->kz_option_id
                    : null,

                // headwear specific
                    'embroidery_option_id'  => ($variantOption && $variant->model->template->category->isHeadwear())
                        ? $variantOption->kz_option_id
                        : null,
                    'stitches'            => ($variant->model->template->category->isHeadwear() && $variant->product)
                        ? $variant->product->getMeta(Product::META_STITCHES)
                        : null,
                    'thread_colors'       => ($variant->model->template->category->isHeadwear() && $variant->product)
                        ? $variant->product->getMeta(Product::META_THREAD_COLORS)
                        : null,

                'art_file_url'        => $designerFileFrontChest && $designerFileFrontChest->file
                    ? url($designerFileFrontChest->file->url())
                    : null,

                'preview_url'         => (
                        $variant->mockups
                        && $variant->mockups->first()
                        && $variant->mockups->first()->file
                    )
                    ? url($variant->mockups->first()->file->url())
                    : null,

                'design_preview_url'  => (
                        $variant->product
                        && $variant->product->filePreview()
                        && $variant->product->filePreview()->file
                    )
                    ? url($variant->product->filePreview()->file->url())
                    : null,

                'print_size'          => $designerFileFrontChest
                    ? $designerFileFrontChest->getPrintSize()
                    : null,
                'design_location'     => $designerFileFrontChest
                    ? $designerFileFrontChest->getPrintPosition()
                    : null,

                // back
                    'print_side' => $printSide,

                    'art_file_url_back'    => $designerFileBack && $designerFileBack->file
                        ? url($designerFileBack->file->url())
                        : null,
                    'preview_url_back'     => (
                            $variant->mockupsBack
                            && $variant->mockupsBack->first()
                            && $variant->mockupsBack->first()->file
                        )
                        ? url($variant->mockupsBack->first()->file->url())
                        : null,
                    'print_size_back'      => $designerFileBack
                        ? $designerFileBack->getPrintSize()
                        : null,
                    'design_location_back' => $designerFileBack
                        ? $designerFileBack->getPrintPosition()
                        : null,

                // TODO: someday we may need this when we will have design locations in variants
                //'design_location'    => $variant->product && $variant->product->clientFiles->first()
                //    ? $variant->product->clientFiles->first()->design_location
                //    : null
            ];
        }

        return $items;
    }

	public function transform(Order $order)
	{
	    return [
	        'source'                => KZApi::SOURCE,
            'external_store_domain' => $order->store->domain,
            'external_order_id'     => $order->id,
            'external_order_number' => $order->orderNumber(),
            'total'                 => Money::i()->amount($order->total()),
            'shipping_cost'         => Money::i()->amount($order->getShippingPrice()),
            'shipping_method'       => $order->getShippingMethodKZ(),
            'price_modifier'        => $order->price_modifier,
            'firstname'             => $order->user->first_name,
            'lastname'              => $order->user->last_name,
            'email'                 => $order->user->email,
            's_firstname'           => $order->shipping_meta->first_name,
            's_lastname'            => $order->shipping_meta->last_name,
            's_address'             => $order->shipping_meta->address1,
            's_address_2'           => $order->shipping_meta->address2,
            's_city'                => $order->shipping_meta->city,
            's_county'              => null,
            's_state'               => $order->shipping_meta->province
                ? $order->shipping_meta->province
                : $order->shipping_meta->province_code,
            's_country'             => $order->shipping_meta->country_code
                ? $order->shipping_meta->country_code
                : $order->shipping_meta->country,
            's_zipcode'             => $order->shipping_meta->zip,
            's_phone'               => $order->shipping_meta->phone,
            'inserts'               => $order->store->inserts,
            'items'                 => $this->items($order)
	    ];
	}
}
