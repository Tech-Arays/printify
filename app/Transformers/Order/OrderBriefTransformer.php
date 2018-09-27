<?php

namespace App\Transformers\Order;

use Gate;
use League\Fractal;

use App\Transformers\Transformer;
use App\Components\Money;
use App\Models\Order;
use App\Transformers\ProductVariant\ProductVariantFullTransformer;

class OrderBriefTransformer extends Transformer
{
	public function transform(Order $order)
	{
	    return [
	        'id'                        => $order->id,
	        'order_number'              => $order->orderNumber(),

            'status'                    => $order->status,
            'statusName'                => $order->getStatusName(),

            'payment_status'            => $order->payment_status,
            'paymentStatusName'         => $order->getPaymentStatusName(),

            'fulfillment_status'        => $order->fulfillment_status,
            'fulfillmentStatusName'     => $order->getFulfillmentStatusName(),

            'refund_status'             => $order->refund_status,
            'refund_status_comment'     => $order->refund_status_comment,
            'refundStatusName'          => $order->getRefundStatusName(),

            'action_required'           => $order->action_required,

            'total'                     => $order->total(),
            'subtotal'                  => $order->subtotal(),
            'profit'                    => $order->profit(),

            'shipping_method'           => $order->shipping_method,
            'shippingPrice'             => $order->getShippingPrice(),

            'shipping_retail_costs'     => $order->shipping_retail_costs,
            'customerShippingRetailCostsPrice' => $order->customerShippingRetailCostsPrice(),

            'customer_paid_price'       => $order->customerPaidPrice(),
            'customerPaidPrice'         => $order->customerPaidPrice(),
            'customer_meta'             => $order->customer_meta,

            'shipping_meta'             => $order->shipping_meta,
            'billing_meta'              => $order->billing_meta,
            'createdAt'                 => $order->createdAtTZ(),
            'updatedAt'                 => $order->updatedAtTZ(),
            'providerUrl'               => $order->providerUrl(),

            'tracking_number'           => $order->tracking_number,

            'isPaid'                    => $order->isPaid(),

            'policy'                    => $this->includePolicies($order)
	    ];
	}
}
