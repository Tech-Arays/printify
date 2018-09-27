<?php

namespace App\Http\Requests\Dashboard\Store;

use App\Http\Requests\Request;

class StoreConnectConfirmFormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'string|required|max:255',
            'hmac' => 'string|required|max:255',
            'shop' => 'string|required|max:255'
        ];
    }
}
