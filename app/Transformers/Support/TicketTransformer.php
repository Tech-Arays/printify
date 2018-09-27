<?php

namespace App\Transformers\Support;

use League\Fractal;

use App\Models\SupportRequest;

class TicketTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        
    ];
    
	public function transform(SupportRequest $supportRequest)
	{
	    return [
            'id' => $supportRequest->id,
            'subject' => $supportRequest->subject,
            'text' => $supportRequest->text,
            'created_at' => $supportRequest->createdAt(),
            'updated_at' => $supportRequest->updatedAt()
	    ];
	}
}
