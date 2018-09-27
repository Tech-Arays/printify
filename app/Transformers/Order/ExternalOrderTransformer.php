<?php

namespace App\Transformers\Order;

use DateTime;

use App\Models\Order;
use App\Transformers\Transformer;

class ExternalOrderTransformer extends Transformer
{
    public function transform($json)
	{
        $status = 'open';
        if ($json->cancelled_at) {
            $status = 'cancelled';
        }

        if ($json->closed_at) {
            $status = 'closed';
        }

	    return [
	        'id'                       => $json->id,
            'exists'                   => (bool)Order::findByProviderId($json->id),
            'name'                     => $json->name,
            'status'                   => $status,
            'created_at'               => new DateTime($json->created_at),
            'customer' => [
                'id'    => $json->customer->id,
                'email' => $json->customer->email
            ]
	    ];
	}
}
