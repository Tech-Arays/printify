<?php

namespace App\Transformers\PriceModifier;

use League\Fractal;

use App\Models\PriceModifier;
use App\Transformers\User\UserBriefTransformer;
use App\Transformers\Product\ProductModelTemplateBriefTransformer;

class PriceModifierBriefTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'user',
        'template'
    ];

    public function includeUser(PriceModifier $modifier)
    {
        $user = $modifier->user;
        if ($user) {
            return $this->item($user, new UserBriefTransformer);
        }
    }

    public function includeTemplate(PriceModifier $modifier)
    {
        $template = $modifier->template;
        if ($template) {
            return $this->item($template, new ProductModelTemplateBriefTransformer);
        }
    }

	public function transform(PriceModifier $modifier)
	{
	    return [
	        'id'        => $modifier->id,
	        'modifier'  => $modifier->modifier
	    ];
	}
}
