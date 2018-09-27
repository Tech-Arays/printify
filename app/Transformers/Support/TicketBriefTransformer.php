<?php

namespace App\Transformers\Support;

use League\Fractal;

use App\Models\SupportRequest;

class TicketBriefTransformer extends Fractal\TransformerAbstract
{
	public function transform(SupportRequest $supportRequest)
	{
	    return [
            'id' => $supportRequest->id,
            'subject' => $supportRequest->subject,
            'created_at' => $supportRequest->createdAt()
	    ];
	}
}
