<?php

namespace App\Transformers\User;

use League\Fractal;

use App\Models\User;

class UserBriefTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [

    ];

	public function transform(User $user)
	{
	    return [
	        'id'        => $user->id,
            'name'      => $user->getName(),
	        'email'     => $user->email,
            'photo_url' => $user->photo_url
	    ];
	}
}
